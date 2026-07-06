# Project Summary — Sistem Penjadwalan Praktikum (Greedy Algorithm)

> **Skripsi / Tugas Akhir** — Aplikasi penjadwalan praktikum laboratorium sekolah berbasis web dengan algoritma **Greedy** untuk automasi pembuatan jadwal.

---

## 1. Latar Belakang

Penjadwalan praktikum di sekolah/madrasah sering dilakukan secara manual, memakan waktu lama dan rawan bentrok (guru, laboratorium, maupun slot jam). Proyek ini membangun sistem otomatis yang:

1. **Mengelola** master data (guru, mata pelajaran, lab, sesi, kelas, ketersediaan guru).
2. **Men-generate** jadwal praktikum secara otomatis menggunakan **algoritma Greedy**.
3. **Memublikasikan** jadwal yang sudah tervalidasi ke siswa melalui halaman publik (tanpa login).

---

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend Framework** | Laravel 13.11.2 |
| **PHP** | 8.4.21 |
| **Admin Panel** | Filament v3 |
| **Frontend (admin)** | Filament + Tailwind via Vite |
| **Frontend (publik)** | Blade + Tailwind via CDN |
| **Database** | MySQL / MariaDB |
| **Timezone** | Asia/Jakarta (dipaksa di controller publik) |
| **Carbon** | Untuk parsing & formatting datetime |
| **Eloquent ORM** | Relasi model + query builder |

---

## 3. Struktur Folder (intinya)

```
app/
├── Models/
│   ├── User.php
│   ├── Guru.php
│   ├── MataPelajaran.php
│   ├── Laboratorium.php
│   ├── Kelas.php
│   ├── SesiPraktikum.php
│   ├── KetersediaanGuru.php
│   └── Jadwal.php                    ← tabel utama
├── Services/
│   └── GreedySchedulerService.php    ← algoritma greedy
├── Http/Controllers/
│   └── JadwalPublicController.php    ← halaman publik /jadwal
├── Filament/
│   ├── Pages/JadwalSaya.php          ← halaman guru: jadwal sendiri
│   ├── Resources/                    ← CRUD admin per model
│   └── Providers/AdminPanelProvider.php
resources/views/
├── public/                           ← blade publik (no Filament)
│   ├── layouts/public.blade.php
│   ├── _jadwal-table.blade.php       ← partial tabel
│   └── jadwal.blade.php
└── filament/                         ← blade Filament
routes/web.php                        ← /jadwal publik route
```

---

## 4. Model Data & Relasi

### Entity-Relationship (ringkas)

```
┌─────────────┐         ┌──────────────────┐
│  gurus      │←────────│ ketersediaans    │
│             │  hasMany│ (ketersediaan)   │
└──────┬──────┘         └──────────────────┘
       │ belongsToMany
       │
       │          ┌──────────────────┐
       ├─────────→│ mata_pelajarans  │
       │          │ - total_jp       │
       │          │ - max_jp_per_sesi│
       │          └──────────────────┘
       │
       │  hasMany  ┌──────────────────────────────┐
       └──────────→│ jadwals (status: draft|pub)   │
                  │ - hari, jam_mulai, jam_selesai│
                  └──┬──────────┬──────────┬──────┘
                     │          │          │
                     ↓          ↓          ↓
               ┌─────────┐ ┌─────────┐ ┌─────────┐
               │mata_    │ │laborat- │ │kelas    │
               │pelajaran│ │orium    │ │         │
               └─────────┘ └─────────┘ └─────────┘
```

### Model `Jadwal` (tabel utama)

| Field | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | PK |
| `guru_id` | FK → gurus | Guru pengajar |
| `mata_pelajaran_id` | FK → mata_pelajarans | Mapel |
| `laboratorium_id` | FK → laboratoriums | Lab |
| `kelas_id` | FK → kelas (nullable) | Kelas |
| `sesi_praktikum_id` | FK → sesi_praktikums (nullable) | Sesi jam |
| `kelas` | string (legacy) | Cache nama kelas |
| `hari` | enum | Senin–Sabtu |
| `jam_mulai` | time | Jam mulai |
| `jam_selesai` | time | Jam selesai |
| `status` | enum | `draft` / `published` |

---

## 5. Algoritma Greedy — Detail

### Apa itu Algoritma Greedy?

Algoritma **Greedy** adalah strategi pemecahan masalah yang pada setiap langkahnya **mengambil pilihan terbaik yang tersedia saat itu** (locally optimal) dengan harapan menghasilkan solusi optimal global.

**Ciri utama:**
- Tidak melihat ke depan (no backtracking)
- Tidak mempertimbangkan konsekuensi jangka panjang
- Cepat dan sederhana
- Cocok untuk masalah optimasi tertentu di mana pilihan optimal lokal = optimal global (atau cukup dekat)

### Bagaimana Greedy Diterapkan di Proyek Ini

Lokasi: `app/Services/GreedySchedulerService.php`  
File ini dipanggil dari tombol **"Generate Jadwal"** di halaman Filament `Jadwal`.

### Struktur Loop (5 tingkat bersarang)

```
for each kelas:                # level 1: kelas
  for each mapel:              # level 2: mata pelajaran
    sisaJp = mapel.total_jp   # inisialisasi JP tersisa
    while sisaJp > 0:          # level 3: selama JP belum terjadwal
      for each guru (qualified):  # level 4: guru yang bisa ajar mapel
        for each hari (Senin-Sabtu):  # level 5a: coba setiap hari
          for each sesi jam:           # level 5b: coba setiap slot
            if slot valid:
              SCHEDULE → break (continue 5)
```

### Aturan Validasi (Constraint Checking)

Sebelum menjadwalkan, greedy harus memastikan **5 constraint** terpenuhi. Jika salah satu gagal, langsung skip ke slot berikutnya (tidak rekursif).

#### ① Batas JP per Sesi

```php
if ($jpSlot > $mapel->maksimal_jp_per_sesi) {
    continue;
}
```

Slot tidak boleh melebihi `maksimal_jp_per_sesi` mapel (mis. max 4 JP per sesi, padahal slot punya 6 JP → skip).

#### ② Ketersediaan Guru

```php
$tersedia = $guru->ketersediaans()
    ->where('hari', $hari)
    ->where('jam_mulai', '<=', $jamMulai)
    ->where('jam_selesai', '>=', $jamSelesai)
    ->exists();
```

Guru harus punya record `KetersediaanGuru` di hari yang sama dengan slot yang menutupi seluruh rentang jam.

#### ③ Bentrok Guru

```php
$guruBentrok = Jadwal::query()
    ->where('guru_id', $guru->id)
    ->where('hari', $hari)
    ->where(function ($q) use ($jamMulai, $jamSelesai) {
        $q->where('jam_mulai', '<', $jamSelesai)
          ->where('jam_selesai', '>', $jamMulai);
    })
    ->exists();
```

**Rumus overlap:** dua interval bentrok jika `mulai₁ < selesai₂` DAN `selesai₁ > mulai₂`.

#### ④ Laboratorium Tersedia

```php
$lab = Laboratorium::query()
    ->whereDoesntHave('jadwals', function ($q) use ($hari, $jamMulai, $jamSelesai) {
        $q->where('hari', $hari)
          ->where(function ($q2) use ($jamMulai, $jamSelesai) {
              $q2->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai);
          });
    })
    ->first();
```

Mengambil **lab pertama yang tidak bentrok** pada hari & jam tersebut (sub-greedy: lab paling sedikit indeks yang free).

#### ⑤ Penentuan JP yang Dipakai

```php
$jpDigunakan = min($jpSlot, $sisaJp);
```

Sisa JP mapel mungkin lebih kecil dari slot. Mis. sisa = 2 JP, slot = 4 JP → hanya pakai 2 JP (efisien).

### Strategi Greedy: "First Fit Wins"

```php
continue 5; // Magic! Keluar dari 5 level loop sekaligus
```

Setelah slot **valid**, langsung:
1. Simpan jadwal dengan `Jadwal::create([...])`
2. Kurangi `sisaJp -= $jpDigunakan`
3. **Langsung lanjut ke JP berikutnya** tanpa mencoba slot/kelas lain yang mungkin lebih optimal

Ini adalah **local optimum greedy** — begitu ketemu slot yang bisa dipakai, langsung ambil. Tidak ada kemungkinan untuk "menyimpan slot bagus untuk mapel yang lebih butuh".

### Safety Break

```php
if ($sisaJp > 0) {
    break; // hindari infinite loop
}
```

Jika setelah semua iterasi tidak ada slot valid sama sekali, keluar dari while loop untuk hindari loop tak hingga.

### Karakteristik Greedy dalam Konteks Ini

| Aspek | Detail |
|---|---|
| **Kompleksitas waktu** | O(K × M × G × H × S × C) — di mana K=kelas, M=mapel, G=guru, H=hari, S=sesi, C=constraint check |
| **Optimal?** | **Tidak dijamin optimal global.** Bisa menghasilkan jadwal yang "cukup baik" tapi belum tentu paling efisien. |
| **Deterministik?** | Ya, untuk input sama → output sama (karena loop urutan tetap). |
| **Kelemahan** | Bisa ada JP yang tidak terjadwal jika constraints terlalu ketat (ketersediaan guru minim, lab terbatas). |
| **Keunggulan** | Cepat, sederhana, mudah di-debug, implementasi pendek. |

### Diagram Alur (Flowchart)

```
[Start]
   │
   ↓
┌──────────────────────────────────────────┐
│ Hapus semua jadwal lama (truncate)       │
└──────────────────┬───────────────────────┘
                   ↓
┌──────────────────────────────────────────┐
│ Untuk setiap kelas                       │
│   Untuk setiap mapel                     │
│     sisaJp = mapel.total_jp              │
│     ┌────────────────────────────────┐   │
│     │ WHILE sisaJp > 0:              │   │
│     │   Untuk setiap guru qualified  │   │
│     │     Untuk setiap hari          │   │
│     │       Untuk setiap sesi        │   │
│     │         Cek 5 constraint ──┐   │   │
│     │         ┌──────────────────┘   │   │
│     │         ↓                      │   │
│     │       Lolos semua?             │   │
│     │         Ya → CREATE jadwal     │   │
│     │         Tidak → skip slot ini  │   │
│     │       continue 5 (next JP)     │   │
│     │     safety break if stuck      │   │
│     └────────────────────────────────┘   │
└──────────────────────────────────────────┘
                   ↓
                 [Done]
```

### Contoh Konkret

Misalkan:
- Kelas 4A, Mapel "Jaringan" (total 8 JP, max 4 JP/sesi)
- Guru: Pak Budi (qualified, tersedia Senin 08:00–12:00)
- Sesi: Sesi1 (08:00–11:30 = 4 JP)

Iterasi greedy:
1. Coba kelas 4A, mapel Jaringan, sisa JP = 8
2. Coba Pak Budi, hari Senin, sesi Sesi1
3. Slot = 4 JP, max/sesi = 4 JP → ✅ lolos ①
4. Pak Budi punya ketersediaan Senin 08:00–12:00 → ✅ lolos ②
5. Belum ada jadwal Pak Budi Senin pagi → ✅ lolos ③
6. Lab Jaringan 1 belum dipakai Senin 08:00–11:30 → ✅ lolos ④
7. JP dipakai: min(4, 8) = 4 JP
8. **CREATE Jadwal**, sisa JP = 4
9. Loop lagi: coba slot berikutnya...

Setelah 2 iterasi, sisa JP = 0 → mapel Jaringan selesai.

---

## 6. Halaman Publik (Siswa)

### Route

```php
// routes/web.php
Route::get('/jadwal', [JadwalPublicController::class, 'index'])->name('public.jadwal');
```

### Controller

File: `app/Http/Controllers/JadwalPublicController.php`

**Tanggung jawab:**

1. Load dropdown filter (kelas, guru, mapel, hari)
2. Parse query params: `hari`, `kelas`, `guru`, `mapel`
3. Tentukan hari aktif: override dari URL atau default **hari ini** (TZ Asia/Jakarta)
4. Query jadwal dengan eager loading relasi, filter `status = 'published'`
5. Grouping:
   - **Tanpa filter kelas** → group by `kelas_id` (setiap kelas = 1 section)
   - **Dengan filter kelas** → group by `hari`
6. Render view `resources/views/public/jadwal.blade.php`

### View Architecture

```
public/jadwal.blade.php        ← main view (header, filter, ringkasan, result)
    └── includes
        └── public/_jadwal-table.blade.php   ← partial tabel + mobile cards
```

### Fitur Filter

| Filter | URL Param | Tipe | Default |
|---|---|---|---|
| Hari | `?hari=Senin` | enum | hari ini (TZ Asia/Jakarta) |
| Kelas | `?kelas=4` | id | semua kelas |
| Guru | `?guru=6` | id | semua guru |
| Mapel | `?mapel=2` | id | semua mapel |

Validation: ID yang tidak valid (non-numerik, ≤0, atau ID yang tidak ada di tabel) di-silent-drop (tidak auto-fallback ke "semua") — sesuai requirement strict validation.

### Responsive Design

- **Desktop (≥sm):** Tabel HTML dengan kolom Hari | Jam | Mapel | Guru | Lab
- **Mobile (<sm):** Card list vertikal dengan badge hari

---

## 7. Panel Admin (Filament)

URL: `/admin`  
Konfigurasi: `app/Providers/Filament/AdminPanelProvider.php`

### Resources

| Resource | URL | Model | Akses |
|---|---|---|---|
| Guru Resource | `/admin/guru` | Guru | admin |
| Mata Pelajaran | `/admin/mata-pelajaran` | MataPelajaran | admin |
| Laboratorium | `/admin/laboratorium` | Laboratorium | admin |
| Sesi Praktikum | `/admin/sesi-praktikum` | SesiPraktikum | admin |
| Kelas | `/admin/kelas` | Kelas | admin |
| Ketersediaan Guru | `/admin/ketersediaan-guru` | KetersediaanGuru | admin |
| Jadwal | `/admin/jadwal` | Jadwal | admin |

### Custom Page

- `JadwalSaya` (icon kalender) — khusus role `guru`, menampilkan jadwal mengajar sendiri (auto-filter by `guru_id` user login).

### Akses Kontrol

```php
// User::canAccessPanel()
return user->role === 'admin' || user->role === 'guru';

// JadwalSaya::canAccess()
return user->role === 'guru';
```

---

## 8. Alur End-to-End

```
┌─────────────────────────────────────────────────────────────────┐
│ FASE 1: SETUP DATA (manual oleh admin)                          │
│   - Tambah mata pelajaran (total_jp, max_jp_per_sesi)           │
│   - Tambah laboratorium (kapasitas, lokasi)                     │
│   - Tambah sesi praktikum (jam_mulai, jam_selesai, jumlah_jp)    │
│   - Tambah kelas (tingkat, jurusan, nama_kelas)                  │
│   - Tambah guru (NIP, nama, mataPelajarans pivot)                │
│   - Tambah ketersediaan guru (hari, jam)                        │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│ FASE 2: GENERATE OTOMATIS (Greedy)                              │
│   Admin klik "Generate Jadwal" di /admin/jadwal                │
│   → Truncate jadwals                                          │
│   → GreedySchedulerService->generate()                          │
│   → Jadwal baru tersimpan dengan status='draft'                 │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│ FASE 3: REVIEW & EDIT (manual)                                  │
│   Admin review jadwal di /admin/jadwal                          │
│   - Edit jadwal yang perlu diubah                              │
│   - Hapus jadwal yang bentrok                                   │
│   - Tambah jadwal manual jika greedy tidak menjadwalkan         │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│ FASE 4: PUBLISH (manual)                                        │
│   Admin ubah status jadwal dari 'draft' ke 'published'          │
│   (saat ini via edit satu-satu; bisa dikembangkan bulk action)   │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│ FASE 5: SISWA MELIHAT JADWAL                                    │
│   Siswa buka /jadwal (tanpa login)                              │
│   - Default: lihat semua kelas yang punya jadwal hari ini       │
│   - Filter: hari, kelas, guru, mapel                            │
│   - Hanya jadwal berstatus 'published' yang tampil              │
└─────────────────────────────────────────────────────────────────┘
```

---

## 9. Limitasi & Pengembangan Lanjutan

### Limitasi Saat Ini

| Limitasi | Penjelasan |
|---|---|
| **Greedy tidak optimal global** | Bisa ada slot JP yang tidak terjadwal jika constraints ketat |
| **Truncate destructive** | Generate ulang menghapus semua jadwal lama tanpa backup |
| **Tidak ada conflict detection UI** | User harus teliti saat edit manual |
| **Publish satu-satu** | Tidak ada bulk action "publish all draft" |
| **Generate langsung sync** | Tidak ada progress bar / background job |

### Potensi Pengembangan

- **Algoritma alternatif**: coba *backtracking*, *CSP*, atau *Genetic Algorithm* untuk hasil lebih optimal
- **Constraint tambahan**: preferensi guru, beban mengajar merata, jarak antar-jadwal per kelas
- **Visualization**: kalender mingguan (drag-drop reschedule)
- **Notifikasi**: email/WhatsApp ke guru saat jadwal baru di-publish
- **Riwayat**: track perubahan jadwal per semester
- **Import/Export**: jadwal ke Excel/CSV untuk arsip

---

## 10. Cara Menjalankan (Quick Start)

```bash
# 1. Install dependency
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migrate + seed
php artisan migrate --seed

# 4. Buat user admin pertama
php artisan make:filament-user

# 5. Jalankan server
php artisan serve

# 6. Login
# http://127.0.0.1:8000/admin

# 7. Setup data: Mapel → Lab → Sesi → Kelas → Guru → Ketersediaan
# 8. Generate jadwal di /admin/jadwal
# 9. Publish jadwal (edit status dari draft → published)
# 10. Lihat di /jadwal (publik)
```

---

## 11. Ringkasan Algoritma Greedy (TLDR)

> Greedy di sini = **"First-Fit Wins"**: cari slot valid pertama (kelas, mapel, guru, hari, sesi, lab) yang lolos 5 constraint, langsung pakai, lanjut ke JP berikutnya. Cepat & sederhana, tapi tidak menjamin optimal global.

| Kelebihan | Kekurangan |
|---|---|
| Implementasi pendek (~250 baris) | Bisa terjebak di solusi sub-optimal |
| O(K·M·G·H·S·C) — cepat untuk data realistis | Tidak ada backtracking |
| Mudah di-debug (output deterministik) | Bisa ada JP yang tidak terjadwal |
| Cocok untuk masalah constrained scheduling | Truncate-dan-regenerate: tidak ada incremental update |

---

**Dokumen terkait:**
- [cara-penggunaan.md](./cara-penggunaan.md) — Panduan pengguna (admin, guru, siswa)
- [README.md](../README.md) — Info framework Laravel default