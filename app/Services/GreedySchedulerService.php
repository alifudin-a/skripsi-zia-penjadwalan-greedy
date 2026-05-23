<?php

namespace App\Services;

// Import model yang dibutuhkan
use App\Models\Jadwal;
use App\Models\Laboratorium;
use App\Models\MataPelajaran;

class GreedySchedulerService
{
    /**
     * Daftar hari aktif sekolah
     * Akan digunakan scheduler untuk mencoba generate jadwal
     */
    protected array $hariList = [
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
    ];

    /**
     * Daftar slot jam yang boleh dipakai
     * Format:
     * [jam_mulai, jam_selesai]
     */
    protected array $jamSlots = [
        ['07:00', '09:00'],
        ['09:00', '11:00'],
        ['13:00', '15:00'],
    ];

    /**
     * Daftar kelas
     * Untuk sementara masih hardcoded
     * nanti bisa dipindah ke tabel kelas
     */
    protected array $kelasList = [
        'X TKJ 1',
        'X TKJ 2',
        'XI RPL 1',
    ];

    /**
     * Method utama untuk generate jadwal
     */
    public function generate(): void
    {
        /**
         * Ambil semua mata pelajaran
         */
        $mapels = MataPelajaran::all();

        /**
         * Loop semua kelas
         */
        foreach ($this->kelasList as $kelas) {

            /**
             * Loop semua mata pelajaran
             */
            foreach ($mapels as $mapel) {

                /**
                 * Ambil guru yang mengajar mapel ini
                 * relasi many-to-many:
                 * guru_mata_pelajaran
                 */
                $gurus = $mapel->gurus;

                /**
                 * Loop semua guru
                 */
                foreach ($gurus as $guru) {

                    /**
                     * Loop semua hari
                     */
                    foreach ($this->hariList as $hari) {

                        /**
                         * Loop semua slot jam
                         */
                        foreach ($this->jamSlots as $slot) {

                            /**
                             * Pecah array slot menjadi:
                             * jam mulai dan jam selesai
                             */
                            [$jamMulai, $jamSelesai] = $slot;

                            /**
                             * CEK KETERSEDIAAN GURU
                             *
                             * Guru dianggap tersedia jika:
                             * - hari sesuai
                             * - jam_mulai <= slot mulai
                             * - jam_selesai >= slot selesai
                             *
                             * Contoh:
                             * guru tersedia 07:00 - 15:00
                             * maka slot 09:00 - 11:00 valid
                             */
                            $tersedia = $guru->ketersediaans()
                                ->where('hari', $hari)
                                ->where('jam_mulai', '<=', $jamMulai)
                                ->where('jam_selesai', '>=', $jamSelesai)
                                ->exists();

                            /**
                             * Jika guru tidak tersedia
                             * lanjut ke slot berikutnya
                             */
                            if (! $tersedia) {
                                continue;
                            }

                            /**
                             * CEK BENTROK GURU
                             *
                             * Tujuan:
                             * memastikan guru tidak mengajar
                             * di jam yang sama
                             *
                             * Rumus overlap:
                             * jadwal_lama.mulai < jadwal_baru.selesai
                             * DAN
                             * jadwal_lama.selesai > jadwal_baru.mulai
                             */
                            $guruBentrok = Jadwal::query()
                                ->where('guru_id', $guru->id)
                                ->where('hari', $hari)
                                ->where(function ($q) use ($jamMulai, $jamSelesai) {

                                    $q->where('jam_mulai', '<', $jamSelesai)
                                        ->where('jam_selesai', '>', $jamMulai);
                                })
                                ->exists();

                            /**
                             * Jika guru bentrok
                             * lanjut ke slot berikutnya
                             */
                            if ($guruBentrok) {
                                continue;
                            }

                            /**
                             * CARI LABORATORIUM KOSONG
                             *
                             * Ambil lab yang:
                             * - tidak dipakai di hari yang sama
                             * - tidak overlap jam
                             */
                            $lab = Laboratorium::query()
                                ->whereDoesntHave('jadwals', function ($q) use ($hari, $jamMulai, $jamSelesai) {

                                    $q->where('hari', $hari)
                                        ->where(function ($q2) use ($jamMulai, $jamSelesai) {

                                            $q2->where('jam_mulai', '<', $jamSelesai)
                                                ->where('jam_selesai', '>', $jamMulai);
                                        });
                                })
                                ->first();

                            /**
                             * Jika tidak ada lab kosong
                             * lanjut ke slot berikutnya
                             */
                            if (! $lab) {
                                continue;
                            }

                            /**
                             * SIMPAN JADWAL
                             *
                             * Jika semua valid:
                             * - guru tersedia
                             * - guru tidak bentrok
                             * - lab kosong
                             *
                             * maka jadwal dibuat
                             */
                            Jadwal::create([
                                'guru_id' => $guru->id,
                                'mata_pelajaran_id' => $mapel->id,
                                'laboratorium_id' => $lab->id,
                                'kelas' => $kelas,
                                'hari' => $hari,
                                'jam_mulai' => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                                'status' => 'draft',
                            ]);

                            /**
                             * GREEDY STRATEGY
                             *
                             * Setelah menemukan slot valid pertama:
                             * - langsung pakai
                             * - lanjut ke mapel berikutnya
                             *
                             * continue 4 berarti keluar dari:
                             * - slot
                             * - hari
                             * - guru
                             * - mapel
                             */
                            continue 4;
                        }
                    }
                }
            }
        }
    }
}