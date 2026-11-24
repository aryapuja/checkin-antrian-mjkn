# SIMRS - Modul Check-In & Bridging Antrean Mobile JKN

Aplikasi bridging dan manajemen antrean pasien terintegrasi dengan Web Service Antrean RS BPJS Kesehatan (Mobile JKN), disesuaikan untuk implementasi front-office rumah sakit (RSUD Kota Malang).

---

## 1. Ikhtisar Sistem

Modul ini berfungsi sebagai antarmuka operasional staf admisi/front-office untuk memverifikasi kedatangan pasien yang mendaftar melalui aplikasi Mobile JKN maupun antrean offline rumah sakit.

### Karakteristik Arsitektur
- **Hybrid Local-Cloud Source:** Seluruh pembacaan data antrean, pencarian, dan pagination dieksekusi langsung dari database lokal SIMRS (`regonline.reservasi`), menghindari limitasi kuota request maupun latensi jaringan BPJS.
- **Action-Only Bridging:** Komunikasi jaringan ke server BPJS Kesehatan hanya dipicu saat staf melakukan tindakan definitif:
  - **Check-In Pasien:** Mengirim payload waktu kedatangan ke endpoint `POST /antrean/updatewaktu`.
  - **Pembatalan Kunjungan:** Mengirim sinyal pembatalan ke endpoint `POST /antrean/batal` dengan alasan pembatalan tersertifikasi.
- **Asynchronous Background Polling:** Pembaruan antrean berkala (setiap 5 menit) menggunakan AJAX partial update tanpa mereload seluruh DOM dokumen.
- **Intelligent Timer Pause:** Siklus auto-refresh otomatis dijeda saat petugas sedang berinteraksi aktif di dalam tabel atau sedang mengetik parameter pencarian.
- **Automatic Fallback Mode:** Jika kredensial BPJS belum terkonfigurasi di lingkungan staging/development, sistem beroperasi dalam mode simulasi transaksi tanpa melempar fatal exception.

---

## 2. Prasyarat Lingkungan

- PHP >= 8.2 (dengan ekstensi: `pdo_mysql`, `curl`, `openssl`, `mbstring`, `json`)
- Composer >= 2.x
- MySQL >= 8.0 atau MariaDB >= 10.4
- Web Server: Nginx / Apache / Laravel Herd

---

## 3. Instalasi & Setup Lokal

### 1. Kloning Repositori
```bash
git clone https://github.com/username/checkin-mjkn.git
cd checkin-mjkn
```

### 2. Instalasi Dependensi
```bash
composer install --no-dev --optimize-autoloader
```
*(Gunakan `composer install` jika berada di lingkungan development).*

### 3. Konfigurasi Lingkungan
Salin template konfigurasi dan generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan parameter database di file `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=regonline
DB_USERNAME=db_user
DB_PASSWORD=db_password

SESSION_DRIVER=file
CACHE_STORE=file
```

### 4. Konfigurasi Kredensial BPJS Kesehatan
Tambahkan kredensial web service yang diterbitkan oleh BPJS:
```ini
BPJS_CONS_ID=12345
BPJS_SECRET_KEY=secret_key_bpjs
BPJS_USER_KEY=user_key_bpjs
BPJS_ENV=dev # dev | prod_v1 | prod_v2
```

### 5. Jalankan Aplikasi
- **Menggunakan Laravel Herd:**
  ```bash
  herd link checkin-mjkn
  ```
  Akses via browser: `http://checkin-mjkn.test`
- **Menggunakan Artisan Server:**
  ```bash
  php artisan serve
  ```
  Akses via browser: `http://localhost:8000`

---

## 4. Spesifikasi Integrasi BPJS Kesehatan

### Base URL
| Environment | Host URL |
| :--- | :--- |
| **Development** | `https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev` |
| **Production V1** | `https://apijkn.bpjs-kesehatan.go.id/antreanrs` |
| **Production V2** | `https://new-apijkn.bpjs-kesehatan.go.id/antreanrs` |

### Autentikasi Request
Setiap request ke server BPJS menyertakan header HMAC-SHA256:
- `x-cons-id`: Consumer ID
- `x-timestamp`: Unix timestamp (UTC)
- `x-signature`: Base64(HMAC-SHA256(cons_id + "&" + timestamp, secret_key))
- `user_key`: User Key WS BPJS

### Mapping Task ID
Sesuai regulasi BPJS Antrean RS:
- **Pasien Baru (`JENIS = 2`):** Mengirim `taskid = 1` (Mulai waktu tunggu admisi).
- **Pasien Lama (`JENIS = 1`):** Mengirim `taskid = 3` (Mulai waktu tunggu poli).
- **Format Waktu:** Epoch timestamp milidetik (`round(microtime(true) * 1000)`).

---

## 5. Pemetaan Skema Database (`reservasi`)

Aplikasi terhubung langsung ke tabel `reservasi` pada database `regonline`:

| Kolom Database | Tipe Data | Peran di Aplikasi |
| :--- | :--- | :--- |
| `ID` | `char(10)` | Primary Key / Kode Booking antrean yang dikirim ke BPJS |
| `TANGGALKUNJUNGAN`| `date` | Acuan filter tanggal pemeriksaan (`CURDATE()`) |
| `NORM` | `int` | Nomor Rekam Medis pasien |
| `NAMA` | `varchar(50)`| Nama lengkap pasien |
| `TANGGAL_LAHIR` | `date` | Kalkulasi umur pasien |
| `JK` | `enum('L','P')` | Jenis kelamin |
| `POLI_BPJS` | `char(10)` | Kode subspesialis poli rujukan |
| `DOKTER` | `char(50)` | Nama dokter poliklinik |
| `NOMOR_ANTRIAN` | `int` | Urutan nomor antrean |
| `JENIS` | `int` | Identifikasi pasien (1 = Lama, 2 = Baru) |
| `JENIS_APLIKASI`| `int` | Kanal registrasi (2 = Mobile JKN, 22 = Offline) |
| `STATUS` | `int` | Status alur (1 = Belum Check-In, 2 = Terkirim/Sudah, 0 = Batal) |
| `WAKTU_CHECK_IN`| `datetime` | Pencatatan timestamp check-in berhasil |

---

## 6. Pengujian Otomatis

Proyek dilengkapi automated test suite berbasis PHPUnit:
```bash
php artisan test
```

Cakupan pengujian:
1. `test_can_view_index_page`: Memverifikasi responsivitas view dashboard utama.
2. `test_can_get_table_data_ajax`: Memverifikasi struktur JSON, partial HTML, dan kalkulasi metrik.
3. `test_can_checkin_antrean`: Menguji integritas alur bridging check-in dan update status database.
4. `test_can_batal_antrean`: Menguji validasi keterangan pembatalan dan update status pembatalan.

---

## 7. Struktur Direktori Utama

```
app/
├── Http/
│   └── Controllers/
│       └── AntreanController.php     # Endpoint AJAX, aksi check-in, batal, dan filter
├── Models/
│   └── Reservasi.php                 # Eloquent mapping tabel reservasi & accessor usia
└── Services/
    └── BPJS/
        ├── BpjsAntreanService.php    # Service bridging, generator signature, & HTTP client
        └── LZCompressor/             # Library dekompresi respon enkripsi BPJS
config/
└── bpjs.php                          # Konfigurasi endpoint dan environment BPJS
resources/
└── views/
    └── antrean/
        ├── index.blade.php           # Master view dashboard & JS AJAX state controller
        └── partials/
            ├── table_rows.blade.php  # Fragment tabel terisolasi untuk partial refresh
            └── pagination.blade.php  # Fragment komponen navigasi halaman
```

---

## 8. Panduan Deployment Produksi

Sebelum memindahkan ke server produksi rumah sakit:
1. Set `APP_ENV=production` dan `APP_DEBUG=false` di `.env`.
2. Arahkan Document Root Web Server (Nginx/Apache) ke direktori `/public`.
3. Pastikan IP Public server RS sudah terdaftar di daftar whitelist IT BPJS Kesehatan Kantor Cabang.
4. Jalankan optimasi cache:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
