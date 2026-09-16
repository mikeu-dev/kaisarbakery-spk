# 🥖 Kaisar Bakery - Sistem Pendukung Keputusan (SPK) TOPSIS & Presensi QR Code

Sistem Pendukung Keputusan (SPK) untuk penilaian kinerja karyawan terbaik di **Kaisar Bakery** menggunakan metode **TOPSIS** *(Technique for Order Preference by Similarity to Ideal Solution)* yang terintegrasi dengan sistem **Presensi Digital QR Code Dinamis** dan **Export Laporan Excel**.

Aplikasi ini dibangun menggunakan framework **Laravel**, panel admin **Filament**, dan **Livewire**.

---

## 📋 Daftar Isi
1. [Fitur Utama Sistem](#-fitur-utama-sistem)
2. [Kebutuhan Sistem (Prasyarat)](#-kebutuhan-sistem-prasyarat)
3. [Panduan Instalasi (Untuk Pemula / Awam)](#-panduan-instalasi-untuk-pemula--awam)
4. [Cara Menjalankan Aplikasi](#-cara-menjalankan-aplikasi)
5. [Akun Login Default](#-akun-login-default)
6. [Struktur Kriteria Penilaian TOPSIS](#-struktur-kriteria-penilaian-topsis)
7. [Solusi Kendala Umum (Troubleshooting)](#-solusi-kendala-umum-troubleshooting)

---

## ✨ Fitur Utama Sistem

- **Manajemen Data Master**:
  - Data Karyawan (Kode alternatif, nama, jabatan, nomor kontak WhatsApp, dan tautan akun login pengguna).
  - Modal pembuatan dan pengeditan akun login pengguna langsung dari formulir karyawan.
  - Data Kriteria Penilaian & Bobot Preferensi (C1 - C5).
- **Presensi Digital Karyawan**:
  - Generator **QR Code Dinamis** (berubah otomatis setiap 15-30 detik untuk mencegah manipulasi/titip presensi).
  - Scanner QR Code kamera untuk karyawan saat presensi masuk & pulang.
  - Pencatatan otomatis metode kehadiran (*QR Dinamis* atau *Manual*).
- **Export Laporan Excel Lengkap**:
  - Export rekapitulasi data presensi ke format `.xlsx` menggunakan engine berkinerja tinggi OpenSpout.
  - Pilihan rentang periode: **Per Hari (Harian)**, **Per Minggu (7 Hari)**, **Per Bulan**, **Rentang Bulan**, dan **Per Tahun**.
  - Dilengkapi filter opsional per karyawan dan status kehadiran beserta ringkasan rekapitulasi.
- **Kalkulasi 6 Tahapan Matriks TOPSIS**:
  - Tahap 1: Matriks Keputusan Awal ($X$) & Perhitungan Divisor.
  - Tahap 2: Matriks Ternormalisasi ($R$).
  - Tahap 3: Matriks Ternormalisasi Terbobot ($Y$).
  - Tahap 4: Solusi Ideal Positif ($A^+$) dan Solusi Ideal Negatif ($A^-$).
  - Tahap 5: Jarak Solusi Ideal Euclidean ($D^+$ dan $D^-$).
  - Tahap 6: Nilai Preferensi Relatif ($V_i$) & Perangkingan Alternatif Terbaik dilengkapi tampilan podium juara.
  - Navigasi antar tahapan matriks menggunakan komponen *Tabs*.
  - Penyimpanan riwayat hasil evaluasi per periode bulan/tahun.
  - Tombol cetak dokumen laporan siap cetak (*Print Preview*).

---

## 🛠️ Kebutuhan Sistem (Prasyarat)

Sebelum menjalankan aplikasi, pastikan komputer Anda telah terpasang software berikut:

1. **PHP >= 8.3** *(Sangat disarankan PHP 8.3 atau 8.4)*
   - Rekomendasi pengguna Windows: Pasang paket **[Laragon](https://laragon.org/download/)** atau **[XAMPP](https://www.apachefriends.org/)**.
   - Pastikan ekstensi berikut aktif di file konfigurasi `php.ini`:
     - `curl`
     - `fileinfo`
     - `gd`
     - `mbstring`
     - `openssl`
     - `pdo_sqlite`
     - `sqlite3`
     - `zip`
2. **Composer >= 2.0**
   - Download dan pasang dari: [https://getcomposer.org](https://getcomposer.org/download/)
3. **Node.js >= 20.x & NPM**
   - Download dan pasang dari: [https://nodejs.org](https://nodejs.org/)

---

## 🚀 Panduan Instalasi (Untuk Pemula / Awam)

Ikuti langkah-langkah berikut secara berurutan. Buka aplikasi **Terminal**, **Command Prompt (CMD)**, atau **PowerShell**, lalu arahkan ke folder proyek ini.

### Langkah 1: Salin File Konfigurasi `.env`
Duplikasi file contoh pengaturan menjadi file `.env`:
- **Windows (CMD / PowerShell):**
  ```shell
  copy .env.example .env
  ```
- **Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

---

### Langkah 2: Pasang Pustaka Dependensi PHP
Jalankan perintah berikut untuk mengunduh semua library backend:
```bash
composer install
```
*Tunggu hingga proses selesai dan muncul konfirmasi.*

---

### Langkah 3: Buat Kunci Keamanan Aplikasi (Application Key)
Jalankan perintah untuk membuat kunci enkripsi unik di `.env`:
```bash
php artisan key:generate
```

---

### Langkah 4: Migrasi Database & Isi Data Awal (Seed)
Aplikasi menggunakan database bawaan **SQLite** yang siap pakai tanpa memerlukan konfigurasi database server terpisah:
```bash
php artisan migrate --seed
```
> 💡 **Informasi:** Jika muncul pertanyaan:
> ```text
> The database file does not exist. Would you like to create it?
> ```
> Ketik `yes` lalu tekan **Enter**.
>
> Perintah ini akan membuat file database lokal `database/database.sqlite` dan langsung mengisinya dengan:
> - Akun Admin Administrator Kaisar Bakery.
> - Data 5 Kriteria Penilaian TOPSIS (C1 - C5).
> - Contoh Data 5 Karyawan Alternatif (A1 - A5) beserta riwayat absensi & penilaiannya.

---

### Langkah 5: Pasang Dependensi Tampilan & Kompilasi Aset
Jalankan perintah berikut untuk mengunduh modul visual dan merakit file CSS/JS:
```bash
npm install
npm run build
```

---

## ⚡ Jalur Cepat (Otomatis)
Jika Anda sudah terbiasa dengan baris perintah, langkah 1 hingga 5 di atas dapat dijalankan sekaligus dengan perintah shortcut bawaan:

```bash
composer run setup
php artisan db:seed
```

---

## 💻 Cara Menjalankan Aplikasi

Jalankan perintah berikut pada terminal di folder proyek:

```bash
php artisan serve
```

Server lokal akan berjalan dan menampilkan pesan:
```text
INFO  Server running on [http://127.0.0.1:8000].
```

Buka peramban web (Google Chrome / Microsoft Edge / Mozilla Firefox), kemudian akses alamat:
👉 **[http://127.0.0.1:8000/panel](http://127.0.0.1:8000/panel)**

---

## 🔑 Akun Login Default

Gunakan kredensial berikut untuk menguji dan masuk ke panel sistem:

| Peran (Role) | Username | Alamat Email | Password | Akses Halaman |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin@kaisarbakery.com` | `password` | Semua modul SPK, Presensi, Master Data, & Export |
| **Karyawan 1 (Baker)** | `budi` | `budi@kaisarbakery.com` | `password` | Dashboard & Riwayat Kehadiran |
| **Karyawan 2 (Kasir)** | `dewi` | `dewi@kaisarbakery.com` | `password` | Dashboard & Riwayat Kehadiran |
| **Karyawan 3 (Logistik)** | `eko` | `eko@kaisarbakery.com` | `password` | Dashboard & Riwayat Kehadiran |

> **Catatan:** URL halaman login berada di: `http://127.0.0.1:8000/panel/login`

---

## 📊 Struktur Kriteria Penilaian TOPSIS

Metode TOPSIS pada sistem ini dikonfigurasi berdasarkan dataset skripsi evaluasi kinerja karyawan:

| Kode | Kriteria Penilaian | Bobot ($W$) | Sifat / Atribut | Keterangan |
| :---: | :--- | :---: | :---: | :--- |
| **C1** | Kehadiran | 25% (0.25) | *Benefit* | Berdasarkan rekapitulasi presensi karyawan |
| **C2** | Ketepatan Waktu | 20% (0.20) | *Benefit* | Kedisiplinan jam masuk kerja |
| **C3** | Produktivitas | 25% (0.25) | *Benefit* | Output hasil produksi roti harian |
| **C4** | Kualitas Kerja | 15% (0.15) | *Benefit* | Kerapian, higienitas, dan standar resep |
| **C5** | Kerjasama Tim | 15% (0.15) | *Benefit* | Koordinasi dan loyalitas kerja |

---

## ❓ Solusi Kendala Umum (Troubleshooting)

### 1. Pesan Error: *"Unable to locate file in Vite manifest"*
- **Penyebab:** File aset CSS dan Javascript belum dirakit oleh Vite.
- **Solusi:** Jalankan perintah `npm run build` di terminal.

### 2. Pesan Error: *"Driver [sqlite] not found"* atau *"could not find driver"*
- **Penyebab:** Ekstensi SQLite di konfigurasi PHP belum diaktifkan.
- **Solusi:** Buka file `php.ini` Anda, cari baris:
  ```ini
  ;extension=pdo_sqlite
  ;extension=sqlite3
  ```
  Hapus tanda titik koma (`;`) di awal kedua baris tersebut, simpan file, lalu restart server PHP / terminal.

### 3. Pesan Error: *"Class 'ZipArchive' not found"* saat Export Excel
- **Penyebab:** Ekstensi ZIP di PHP belum diaktifkan.
- **Solusi:** Buka file `php.ini`, hapus tanda titik koma (`;`) pada baris `extension=zip`, simpan file, lalu restart server PHP.

### 4. Pesan: *"Port 8000 is already in use"*
- **Penyebab:** Port 8000 sedang digunakan oleh aplikasi lain atau server sebelumnya belum dimatikan.
- **Solusi:** Jalankan server pada port alternatif:
  ```bash
  php artisan serve --port=8080
  ```
  Lalu buka aplikasi pada: `http://127.0.0.1:8080/panel`

### 5. Ingin Mereset Database ke Kondisi Awal
- **Solusi:** Jalankan perintah berikut untuk mengulang database dan mengisi kembali data bawaan:
  ```bash
  php artisan migrate:fresh --seed
  ```

---

## 📄 Lisensi
Hak Cipta © 2026 Kaisar Bakery. Dikembangkan untuk keperluan operasional dan penelitian Sistem Pendukung Keputusan (SPK) Metode TOPSIS.
