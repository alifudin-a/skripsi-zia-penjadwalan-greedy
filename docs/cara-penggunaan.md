# Cara Penggunaan Aplikasi Penjadwalan Praktikum

> **Penjadwalan Praktikum dengan Algoritma Greedy**  
> Aplikasi berbasis Laravel + Filament untuk mengelola dan mempublikasikan jadwal praktikum laboratorium sekolah secara otomatis.

Dokumen ini menjelaskan cara menggunakan aplikasi dari sisi **admin/guru** (panel Filament) dan **siswa/publik** (halaman publik).

---

## Daftar Isi

- [Akses Aplikasi](#akses-aplikasi)
- [Peran Pengguna](#peran-pengguna)
- [Alur Umum (Workflow)](#alur-umum-workflow)
- [Panel Admin (Filament)](#panel-admin-filament)
  - [1. Mengelola Mata Pelajaran](#1-mengelola-mata-pelajaran)
  - [2. Mengelola Laboratorium](#2-mengelola-laboratorium)
  - [3. Mengelola Sesi Praktikum](#3-mengelola-sesi-praktikum)
  - [4. Mengelola Kelas](#4-mengelola-kelas)
  - [5. Mengelola Guru](#5-mengelola-guru)
  - [6. Mengelola Ketersediaan Guru](#6-mengelola-ketersediaan-guru)
  - [7. Mengelola Jadwal](#7-mengelola-jadwal)
  - [8. Generate Jadwal Otomatis (Greedy)](#8-generate-jadwal-otomatis-greedy)
  - [9. Halaman Jadwal Saya (khusus guru)](#9-halaman-jadwal-saya-khusus-guru)
- [Halaman Publik (Siswa)](#halaman-publik-siswa)
- [Reset Password / Akun](#reset-password--akun)

---

## Akses Aplikasi

| Bagian | URL | Akses |
|---|---|---|
| Panel Admin | `/admin` | Wajib login (admin/guru) |
| Halaman Publik Jadwal | `/jadwal` | Bebas (tanpa login) |

Jalankan server development:

```bash
php artisan serve
```

Lalu buka:
- **Admin:** http://127.0.0.1:8000/admin
- **Publik:** http://127.0.0.1:8000/jadwal

---

## Peran Pengguna

Aplikasi mendukung 2 peran (kolom `role` di tabel `users`):

| Role | Akses |
|---|---|
| `admin` | Semua menu admin (Kelas, Mapel, Lab, Guru, Jadwal, dll.) |
| `guru` | Menu terbatas + halaman **Jadwal Saya** (jadwal mengajar pribadi) |

Hanya user dengan role `admin` atau `guru` yang dapat melewati `canAccessPanel()`. Role lain tidak akan bisa masuk.

---

## Alur Umum (Workflow)

```
┌──────────────────────────────────────────────────────────┐
│  1. Setup Master Data (Mapel, Lab, Sesi, Kelas, Guru)   │
└────────────────────┬─────────────────────────────────────┘
                     ↓
┌──────────────────────────────────────────────────────────┐
│  2. Atur Ketersediaan tiap Guru per Hari (jam tersedia)  │
└────────────────────┬─────────────────────────────────────┘
                     ↓
┌──────────────────────────────────────────────────────────┐
│  3. Klik "Generate Jadwal" (otomatis via Greedy)         │
└────────────────────┬─────────────────────────────────────┘
                     ↓
┌──────────────────────────────────────────────────────────┐
│  4. Review jadwal yang dibuat (status: draft)            │
└────────────────────┬─────────────────────────────────────┘
                     ↓
┌──────────────────────────────────────────────────────────┐
│  5. Edit manual jika perlu → ubah status ke "published" │
└────────────────────┬─────────────────────────────────────┘
                     ↓
┌──────────────────────────────────────────────────────────┐
│  6. Siswa melihat jadwal di halaman publik /jadwal       │
└──────────────────────────────────────────────────────────┘
```

---

## Panel Admin (Filament)

Login di `/admin` → otomatis diarahkan ke **Dashboard**.

### 1. Mengelola Mata Pelajaran

**Menu:** `Mata Pelajaran` (icon buku)

Form input:

| Field | Wajib | Keterangan |
|---|---|---|
| `kode` | ✅ | Kode unik mapel (mis. `JRK`, `BDP`) |
| `nama` | ✅ | Nama mata pelajaran |
| `total_jp` | ❌ | Total jam pelajaran yang harus dicapai (default: 20) |
| `maksimal_jp_per_sesi` | ❌ | Batas JP per sesi (default: 8) |

> **JP = Jam Pelajaran**. Misalnya `total_jp = 8` artinya mapel ini butuh 8 JP dialokasikan ke kelas.

---

### 2. Mengelola Laboratorium

**Menu:** `Laboratorium` (icon gedung)

| Field | Wajib | Keterangan |
|---|---|---|
| `nama` | ✅ | Nama laboratorium (mis. `Lab Jaringan 1`) |
| `kapasitas` | ❌ | Kapasitas ruangan (default: 30) |
| `lokasi` | ❌ | Lokasi / deskripsi singkat |

---

### 3. Mengelola Sesi Praktikum

**Menu:** `Sesi Praktikum`

| Field | Wajib | Keterangan |
|---|---|---|
| `nama_sesi` | ✅ | Nama sesi (mis. `Sesi 1`, `Sesi Pagi`) |
| `jam_mulai` | ✅ | Jam mulai sesi (format HH:MM) |
| `jam_selesai` | ✅ | Jam selesai sesi |
| `jumlah_jp` | ✅ | JP yang dihitung untuk sesi ini |

> Contoh: sesi 08:00–11:30 = 4 JP (asumsi 1 JP = 45 menit).

---

### 4. Mengelola Kelas

**Menu:** `Kelas`

| Field | Wajib | Keterangan |
|---|---|---|
| `nama_kelas` | ✅ | Identifier kelas (mis. `4A`, `5B`) |
| `tingkat` | ❌ | Tingkat kelas (10, 11, 12) |
| `jurusan` | ❌ | Jurusan (TKJ, RPL, MM) |

---

### 5. Mengelola Guru

**Menu:** `Guru` (icon orang)

Form input:

| Field | Wajib | Keterangan |
|---|---|---|
| `nip` | ✅ | NIP guru (pencarian) |
| `nama` | ✅ | Nama lengkap |
| `no_hp` | ❌ | Nomor HP |
| `alamat` | ❌ | Alamat |
| `mataPelajarans` | ❌ | Centang mapel yang bisa diajar (relasi pivot `guru_mata_pelajaran`) |
| **Akun Login** *(hanya saat create)* | | |
| `email` | ✅ saat create | Email untuk login |
| `password` | ✅ saat create | Password (min. 8 karakter) |

> **Catatan:** Akun login hanya bisa dibuat dari halaman create guru. Jika guru sudah ada, akun login tidak bisa ditambah dari sini — minta admin seed user baru.

---

### 6. Mengelola Ketersediaan Guru

**Menu:** `Ketersediaan Guru`

| Field | Wajib | Keterangan |
|---|---|---|
| `guru_id` | ✅ | Guru terkait |
| `hari` | ✅ | Hari tersedia (Senin–Sabtu) |
| `jam_mulai` | ✅ | Jam mulai tersedia |
| `jam_selesai` | ✅ | Jam selesai tersedia |

> **Penting:** Greedy algorithm hanya menempatkan guru pada jam-jam yang tercantum di sini. Semakin lengkap ketersediaan, semakin mudah greedy menemukan slot.

---

### 7. Mengelola Jadwal

**Menu:** `Jadwal`

Tempat semua jadwal praktikum (otomatis atau manual). Tiap record:

| Field | Keterangan |
|---|---|
| `guru` | Guru pengajar |
| `mataPelajaran` | Mata pelajaran |
| `laboratorium` | Lab yang dipakai |
| `kelas` | Kelas yang dijadwalkan |
| `hari` | Hari (Senin–Sabtu) |
| `sesiPraktikum` | Sesi (jam mulai – selesai, JP) |
| `status` | `draft` atau `published` |

Aksi baris:
- **Edit** — ubah field secara manual
- **Hapus** — hapus jadwal

---

### 8. Generate Jadwal Otomatis (Greedy)

**Lokasi:** Halaman `Jadwal` → tombol **Generate Jadwal** (icon petir ⚡) di header tabel.

Apa yang terjadi:

1. **Semua jadwal lama di-truncate** (data `jadwal` dikosongkan)
2. **Service `GreedySchedulerService` dipanggil** untuk generate jadwal baru
3. **Notifikasi sukses** muncul: "Jadwal berhasil digenerate ulang"
4. Semua jadwal baru di-set `status = 'draft'` (perlu direview dulu)

> ⚠️ **PERINGATAN:** Generate ulang akan **menghapus semua jadwal** tanpa konfirmasi. Pastikan Anda sudah review atau backup data sebelum klik tombol ini.

Kapan tombol ini digunakan:

- Pertama kali setup setelah master data lengkap
- Awal semester / tahun ajaran baru
- Setelah ada perubahan master data (guru baru, mapel baru, dll.)

Setelah generate, **review** jadwal yang dibuat. Yang perlu dicek:
- Apakah semua mapel sudah terjadwal (cek `total_jp`)
- Apakah tidak ada guru yang terlalu banyak mengajar
- Apakah laboratorium terdistribusi merata

Jika sudah oke, ubah `status` jadwal dari `draft` ke `published` (manual via Edit, atau bulk via DB) agar tampil di halaman publik.

---

### 9. Halaman Jadwal Saya (khusus guru)

**Menu:** `Jadwal Saya` (icon kalender)

Hanya muncul untuk user dengan `role = 'guru'`. Menampilkan tabel jadwal praktikum yang diajar oleh guru yang sedang login.

Filter otomatis: hanya jadwal dengan `guru_id` = user guru yang sedang login.

---

## Halaman Publik (Siswa)

**URL:** `/jadwal` (tidak perlu login)

Halaman ini menampilkan jadwal praktikum **yang sudah berstatus `published`** saja — jadwal dengan status `draft` tidak akan muncul.

### Tampilan Default

Saat pertama kali dibuka, siswa akan melihat **semua kelas yang punya jadwal pada hari ini** (otomatis dideteksi dari timezone `Asia/Jakarta`).

Judul halaman: **"Jadwal Hari Ini"**

### Fitur Filter

Siswa dapat memfilter jadwal dengan 4 dropdown:

| Filter | Opsi |
|---|---|
| **Hari** | Hari Ini (default), Senin, Selasa, Rabu, Kamis, Jumat, Sabtu |
| **Kelas** | Semua Kelas, atau pilih kelas tertentu |
| **Guru** | Semua Guru, atau pilih guru tertentu |
| **Mata Pelajaran** | Semua Mapel, atau pilih mapel tertentu |

Setelah memilih filter, klik tombol **Tampilkan**.

### Mode Tampilan

1. **Tanpa filter kelas** → jadwal dikelompokkan per kelas. Tiap kelas ditampilkan sebagai card terpisah.
2. **Dengan filter kelas** → jadwal dikelompokkan per hari.
3. **Filter dengan hasil kosong** → tampil empty state dengan pesan informatif.

### Reset Filter

Tombol **Reset ke hari ini** muncul di samping tombol Tampilkan saat ada filter aktif. Klik tombol ini untuk menghapus semua filter.

### Tampilan Responsif

- **Desktop (≥ sm):** Tabel lengkap dengan kolom: **Hari | Jam | Mata Pelajaran | Guru | Laboratorium**
- **Mobile:** Card per jadwal (lebih ringkas, tap-friendly)

---

## Reset Password / Akun

Reset password admin melalui command `php artisan tinker` (atau `php artisan make:filament-user`):

```bash
php artisan tinker --execute="\App\Models\User::where('email','admin@x.com')->update(['password'=>bcrypt('passwordbaru')])"
```

Atau buat user baru via tinker:

```bash
php artisan tinker --execute="\App\Models\User::create(['name'=>'Admin','email'=>'admin@x.com','password'=>bcrypt('password'),'role'=>'admin'])"
```

---

## Troubleshooting

| Masalah | Solusi |
|---|---|
| Generate jadwal gagal / tidak ada jadwal | Cek: master data lengkap? Ketersediaan guru sudah diisi? |
| Halaman publik kosong | Jadwal masih `draft` — ubah ke `published` |
| Guru bentrok jadwal | Tambah ketersediaan guru atau kurangi JP mapel |
| Tidak bisa login | Cek role user harus `admin` atau `guru` |
| Tombol Generate tidak muncul | Kemungkinan role bukan `admin` (cek `AdminPanelProvider`) |

---

Untuk dokumentasi teknis detail tentang algoritma Greedy dan arsitektur, lihat [project-summary.md](./project-summary.md).