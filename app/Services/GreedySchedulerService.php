<?php

namespace App\Services;

// Import model yang dibutuhkan
use App\Models\Jadwal;
use App\Models\Laboratorium;
use App\Models\MataPelajaran;
use App\Models\SesiPraktikum;
use App\Models\Kelas;

class GreedySchedulerService
{
    /**
     * Daftar hari aktif sekolah
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
     * Method utama generate jadwal
     */
    public function generate(): void
    {
        /**
         * Ambil semua mata pelajaran
         */
        $mapels = MataPelajaran::all();
        $sesiPraktikums = SesiPraktikum::all();

        /**
         * LOOP SEMUA KELAS
         */
        foreach (Kelas::all() as $kelas) {

            /**
             * LOOP SEMUA MAPEL
             */
            foreach ($mapels as $mapel) {

                /**
                 * total_jp mapel
                 *
                 * Contoh:
                 * Jaringan = 8 JP
                 */
                $sisaJp = $mapel->total_jp;

                /**
                 * SELAMA MASIH ADA JP YANG BELUM TERJADWAL
                 *
                 * Contoh:
                 * awal = 8
                 * setelah generate 4 JP
                 * sisa = 4
                 */
                while ($sisaJp > 0) {

                    /**
                     * Ambil semua guru
                     * yang bisa mengajar mapel ini
                     */
                    $gurus = $mapel->gurus;

                    /**
                     * LOOP SEMUA GURU
                     */
                    foreach ($gurus as $guru) {

                        /**
                         * LOOP SEMUA HARI
                         */
                        foreach ($this->hariList as $hari) {

                            /**
                             * LOOP SEMUA SLOT JAM
                             */
                            foreach ($sesiPraktikums as $sesi) {

                                /**
                                 * Ambil data slot
                                 */
                                $jamMulai = $sesi->jam_mulai;
                                $jamSelesai = $sesi->jam_selesai;
                                $jpSlot = $sesi->jumlah_jp;

                                /**
                                 * CEK MAXIMAL JP PER SESI
                                 *
                                 * Contoh:
                                 * max_per_sesi = 4
                                 *
                                 * jika slot 6 JP
                                 * maka skip
                                 */
                                if ($jpSlot > $mapel->maksimal_jp_per_sesi) {
                                    continue;
                                }

                                /**
                                 * CEK KETERSEDIAAN GURU
                                 *
                                 * Guru harus:
                                 * - tersedia di hari tersebut
                                 * - jam availability mencakup slot
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
                                 * Rumus overlap:
                                 *
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
                                 * skip
                                 */
                                if ($guruBentrok) {
                                    continue;
                                }

                                /**
                                 * CARI LABORATORIUM KOSONG
                                 *
                                 * Lab tidak boleh dipakai
                                 * di jam yang sama
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
                                 * lanjut slot berikutnya
                                 */
                                if (! $lab) {
                                    continue;
                                }

                                /**
                                 * TENTUKAN BERAPA JP YANG DIPAKAI
                                 *
                                 * Contoh:
                                 *
                                 * sisa JP = 2
                                 * slot JP = 4
                                 *
                                 * maka pakai:
                                 * 2 JP saja
                                 */
                                $jpDigunakan = min($jpSlot, $sisaJp);

                                /**
                                 * SIMPAN JADWAL
                                 */
                                Jadwal::create([
                                    'guru_id' => $guru->id,
                                    'mata_pelajaran_id' => $mapel->id,
                                    'laboratorium_id' => $lab->id,
                                    'kelas_id' => $kelas->id,
                                    'kelas' => $kelas->nama_kelas,
                                    'hari' => $hari,
                                    'jam_mulai' => $jamMulai,
                                    'jam_selesai' => $jamSelesai,
                                    'status' => 'draft',
                                    'sesi_praktikum_id' => $sesi->id,
                                ]);

                                /**
                                 * KURANGI SISA JP
                                 *
                                 * Contoh:
                                 * awal = 8
                                 * dipakai = 4
                                 * sisa = 4
                                 */
                                $sisaJp -= $jpDigunakan;

                                /**
                                 * GREEDY STRATEGY
                                 *
                                 * Setelah ketemu slot valid:
                                 * - langsung pakai
                                 * - lanjut generate JP berikutnya
                                 */
                                continue 5;
                            }
                        }
                    }

                    /**
                     * SAFETY BREAK
                     *
                     * Jika tidak ada slot valid sama sekali,
                     * hindari infinite loop
                     */
                    break;
                }
            }
        }
    }
}
