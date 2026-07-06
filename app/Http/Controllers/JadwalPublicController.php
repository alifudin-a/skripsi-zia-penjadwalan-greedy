<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman publik untuk murid melihat jadwal.
 * Tidak butuh login. Default: tampilkan semua kelas yang punya jadwal hari ini.
 *
 * Filter (semua opsional):
 *   ?hari=Senin     -> override hari (default: hari ini)
 *   ?kelas={id}     -> filter kelas
 *   ?guru={id}      -> filter guru
 *   ?mapel={id}     -> filter mata pelajaran
 */
class JadwalPublicController extends Controller
{
    /**
     * Urutan hari untuk pengelompokan tampilan.
     * Key = nama hari di DB, Value = urutan tampil (1 = paling atas).
     */
    private const HARI_ORDER = [
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
        'Sabtu' => 6,
        'Minggu' => 7,
    ];

    /**
     * Map nama hari Inggris (Carbon) → key DB Indonesia.
     */
    private const HARI_NAME_MAP = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    /**
     * Zona waktu untuk "hari ini". Dipaksa ke Asia/Jakarta agar konsisten
     * apapun konfigurasi app.timezone server.
     */
    private const TZ = 'Asia/Jakarta';

    public function index(Request $request): View
    {
        // Daftar untuk dropdown filter.
        $kelasList = Kelas::query()
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $guruList = Guru::query()
            ->orderBy('nama')
            ->get();

        $mapelList = MataPelajaran::query()
            ->orderBy('nama')
            ->get();

        $today = $this->todayName();

        $hariParam = $this->sanitizeHari($request->query('hari'));
        $kelasId = $this->sanitizeId($request->query('kelas'));
        $guruId = $this->sanitizeId($request->query('guru'));
        $mapelId = $this->sanitizeId($request->query('mapel'));

        // Hari aktif: override dari URL, atau default = hari ini.
        $hari = $hariParam ?? $today;
        $isDefaultHari = $hariParam === null;

        $selectedKelas = $kelasId ? $kelasList->firstWhere('id', $kelasId) : null;
        $selectedGuru = $guruId ? $guruList->firstWhere('id', $guruId) : null;
        $selectedMapel = $mapelId ? $mapelList->firstWhere('id', $mapelId) : null;

        // Query jadwal — selalu filter by hari.
        $query = Jadwal::query()
            ->with(['mataPelajaran', 'guru', 'laboratorium', 'kelasRelasi'])
            ->where('hari', $hari);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }
        if ($guruId) {
            $query->where('guru_id', $guruId);
        }
        if ($mapelId) {
            $query->where('mata_pelajaran_id', $mapelId);
        }

        // Grouping berbeda tergantung apakah user filter kelas.
        if ($kelasId) {
            // Satu kelas → group by hari (untuk konsistensi dengan UX sebelumnya).
            $jadwalsByHari = $query
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari')
                ->sortKeysUsing(function (string $a, string $b): int {
                    return (self::HARI_ORDER[$a] ?? 99) <=> (self::HARI_ORDER[$b] ?? 99);
                });
            $jadwalsByKelas = collect();
        } else {
            // Tanpa filter kelas → tampil semua kelas yang punya jadwal hari ini.
            $jadwalsByHari = collect();
            $jadwalsByKelas = $query
                ->orderBy('kelas_id')
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('kelas_id')
                ->sortKeys();
        }

        $tanggalHariIni = Carbon::now(self::TZ)->translatedFormat('d F Y');

        return view('public.jadwal', [
            'kelasList' => $kelasList,
            'guruList' => $guruList,
            'mapelList' => $mapelList,
            'hariOptions' => array_keys(self::HARI_ORDER),

            'kelas' => $selectedKelas,
            'guru' => $selectedGuru,
            'mapel' => $selectedMapel,
            'hari' => $hari,
            'today' => $today,
            'isDefaultHari' => $isDefaultHari,
            'tanggalHariIni' => $tanggalHariIni,

            'jadwalsByHari' => $jadwalsByHari,
            'jadwalsByKelas' => $jadwalsByKelas,

            'hasActiveFilters' => (bool) ($kelasId || $guruId || $mapelId || ! $isDefaultHari),
        ]);
    }

    private function todayName(): ?string
    {
        $english = Carbon::now(self::TZ)->format('l');
        return self::HARI_NAME_MAP[$english] ?? null;
    }

    private function sanitizeId(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }
        $id = (int) $value;
        return $id > 0 ? $id : null;
    }

    private function sanitizeHari(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $value = trim($value);
        return array_key_exists($value, self::HARI_ORDER) ? $value : null;
    }
}