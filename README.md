# 🚀 BE Test - Installation Guide

Aplikasi ini dibangun menggunakan **Laravel 11** dan membutuhkan **PHP >= 8.3**.

Proyek dapat dijalankan menggunakan:

- 🐳 Docker / Podman (Direkomendasikan)
- 💻 Laragon / XAMPP (Tanpa Docker)

Dokumentasi API (POSTMAN): https://rafiodypras16.postman.co/workspace/Team-Workspace~2eecc86b-fb7a-4b27-880a-608eb8d38dae/collection/55515111-808493f3-8eed-4a1a-a0ee-f10f1435859d?action=share&creator=55515111
---

# 📋 Prasyarat

Pastikan perangkat Anda telah terinstal:

- Git
- Composer
- PHP >= 8.3
- MySQL (untuk instalasi tanpa Docker)

---

# 🐳 Metode 1: Docker / Podman (Direkomendasikan)

Metode ini menggunakan lingkungan terisolasi sehingga konfigurasi lebih konsisten di berbagai sistem operasi.

## 1. Clone Repository

```bash
git clone <repository-url> be_test
cd be_test
```

## 2. Buat File Environment

### Windows (CMD)

```cmd
copy .env.example .env
```

### Linux / Mac / PowerShell

```bash
cp .env.example .env
```

## 3. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=be_test
DB_USERNAME=root
DB_PASSWORD=password
```

## 4. Konfigurasi OAuth 

Konfigurasi key OAuth pada file `.env` sesuai key yang anda miliki pada https://console.cloud.google.com/apis/credentials

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback
```

## 5. Install Dependency

Jalankan Composer langsung dari host:

```bash
composer install
```

## 6. Jalankan Container

### Podman

```bash
podman compose up -d --build
```

### Docker

```bash
docker compose up -d --build
```

> ⚠️ Tunggu sekitar 15–30 detik saat pertama kali menjalankan container agar MySQL selesai melakukan inisialisasi.

## 7. Setup Laravel

### Podman

```bash
# Bersihkan cache konfigurasi
podman compose exec app php artisan config:clear

# Generate application key
podman compose exec app php artisan key:generate

# Migrasi database dan seeder
podman compose exec app php artisan migrate:fresh --seed

# Membuat symbolic link storage
podman compose exec app php artisan storage:link
```

### Docker

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan storage:link
```

## 8. Akses Aplikasi

Buka browser:

```text
http://localhost:8000
```

---

# 💻 Metode 2: Laragon / XAMPP (PHP 8.3+)

Metode ini menjalankan aplikasi langsung pada web server lokal.

## 1. Pindahkan Project

### Laragon

```text
C:\laragon\www\be_test
```

### XAMPP

```text
C:\xampp\htdocs\be_test
```

## 2. Buat File Environment

### Windows (CMD)

```cmd
copy .env.example .env
```

### Linux / Mac / PowerShell

```bash
cp .env.example .env
```

## 3. Buat Database

Jalankan query berikut pada MySQL:

```sql
CREATE DATABASE be_test;
```

## 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=be_test
DB_USERNAME=root
DB_PASSWORD=
```

> Sesuaikan username dan password jika konfigurasi MySQL Anda berbeda.

## 5. Konfigurasi OAuth 

Konfigurasi key OAuth pada file `.env` sesuai key yang anda miliki pada https://console.cloud.google.com/apis/credentials

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback
```

## 6. Install Dependency dan Setup Laravel

```bash
composer install

php artisan key:generate

php artisan migrate:fresh --seed

php artisan storage:link
```

## 7. Jalankan Aplikasi

### Opsi A - Laragon

Reload Laragon lalu akses:

```text
http://be_test.test
```

### Opsi B - Laravel Development Server

```bash
php artisan serve
```

Aplikasi akan tersedia di:

```text
http://127.0.0.1:8000
```

---

# 📦 Perintah Penting

## Menjalankan Migrasi

```bash
php artisan migrate
```

## Menjalankan Seeder

```bash
php artisan db:seed
```

## Membersihkan Cache

```bash
php artisan optimize:clear
```

## Membuat Storage Link

```bash
php artisan storage:link
```

---

# 🏗️ Arsitektur Sistem

Aplikasi ini dibangun menggunakan pendekatan **Layered Architecture** untuk memisahkan tanggung jawab setiap komponen sehingga kode lebih mudah dipelihara, diuji, dan dikembangkan.

## Controller Layer

Controller bertanggung jawab menerima HTTP Request, melakukan validasi input, memanggil business logic, dan mengembalikan HTTP Response dalam format JSON.

Contoh:

* AuthController
* CutiController

## Model Layer

Model digunakan untuk berinteraksi dengan database menggunakan Eloquent ORM.

Entitas utama:

* User
* Cuti

## Middleware Layer

Middleware digunakan untuk melakukan:

* Autentikasi menggunakan Laravel Sanctum
* Pembatasan akses berdasarkan Role (Admin / Employee)

## Storage Layer

Dokumen pendukung pengajuan cuti disimpan pada direktori:

```text
storage/app/public/attachments
```

dan diakses melalui symbolic link Laravel Storage.

---

# 🔐 Alur Autentikasi (Authentication & Authorization)

## Login Konvensional

1. User mengirim email dan password.
2. Sistem melakukan verifikasi menggunakan `Auth::attempt()`.
3. Jika valid, Laravel Sanctum membuat Access Token.
4. Token dikirim kembali ke client.

## Login Google OAuth

1. User diarahkan ke halaman autentikasi Google.
2. Setelah berhasil login, Google mengembalikan informasi pengguna.
3. Sistem akan membuat atau memperbarui data user.
4. Laravel Sanctum menerbitkan Access Token.

## Akses Endpoint Terproteksi

Setiap request ke endpoint privat wajib menyertakan token:

```http
Authorization: Bearer <token>
```

Middleware Sanctum akan memvalidasi token sebelum request diproses.

## Logout

Saat logout, token yang sedang aktif digunakan akan dihapus menggunakan:

```php
$user->currentAccessToken()->delete();
```

Sehingga token tersebut tidak dapat digunakan kembali.

---

# 👥 Role & Authorization

Sistem memiliki dua role utama:

## Employee

Hak akses:

* Mengajukan cuti
* Membatalkan pengajuan cuti yang masih pending
* Melihat riwayat cuti sendiri
* Melihat status pengajuan
* Melihat sisa kuota cuti

## Admin

Hak akses:

* Melihat seluruh data cuti
* Menyetujui pengajuan cuti
* Menolak pengajuan cuti

---

# 📄 Alur Pengajuan Cuti

1. Employee mengirim data:

   * start_date
   * end_date
   * reason
   * attachment

2. Sistem melakukan validasi:

   * Format tanggal valid
   * end_date tidak boleh lebih kecil dari start_date
   * Format file sesuai ketentuan

3. Sistem memeriksa apakah user masih memiliki pengajuan dengan status `pending`.

4. Sistem menghitung jumlah hari cuti yang diajukan.

5. Sistem memeriksa ketersediaan kuota cuti tahunan.

6. Jika kuota mencukupi:

   * Data disimpan
   * Status otomatis menjadi `pending`

7. Jika kuota tidak mencukupi:

   * Request ditolak

---

# 📊 Alur Perhitungan Kuota Cuti Tahunan

Setiap karyawan memiliki kuota cuti maksimal:

```text
12 Hari / Tahun
```

Sistem hanya menghitung cuti yang:

* Berstatus `approved`
* Memiliki irisan tanggal dengan tahun yang sedang dihitung

## Proses Perhitungan

### 1. Ambil seluruh cuti approved

Sistem mengambil seluruh data cuti milik user yang sudah disetujui.

### 2. Cari cuti yang beririsan dengan tahun aktif

Contoh perhitungan tahun:

```text
2025
```

Sistem tetap menghitung:

```text
2025-03-01 s/d 2025-03-05
2024-12-28 s/d 2025-01-03
2025-12-25 s/d 2026-01-05
2024-12-20 s/d 2026-01-10
```

karena seluruh rentang tersebut memiliki irisan dengan tahun 2025.

### 3. Normalisasi Rentang Tanggal

Rentang cuti akan dibatasi ke awal dan akhir tahun yang sedang dihitung.

Contoh:

```text
Cuti:
2024-12-20 s/d 2025-01-10
```

Saat menghitung kuota tahun 2025:

```text
Tanggal Efektif:
2025-01-01 s/d 2025-01-10
```

Sehingga yang dihitung hanya:

```text
10 hari
```

bukan seluruh:

```text
22 hari
```

### 4. Hitung Total Hari Cuti

Setelah seluruh rentang tanggal dinormalisasi, sistem menghitung total hari cuti yang digunakan pada tahun tersebut.

### 5. Hitung Sisa Kuota

Rumus:

```text
Sisa Kuota = 12 - Total Hari Cuti Approved
```

Contoh:

```text
Kuota Tahunan : 12 Hari
Sudah Digunakan : 8 Hari

Sisa Kuota : 4 Hari
```

Apabila hasil perhitungan kurang dari 0, sistem akan mengembalikan nilai 0.

---

# 🔄 Workflow Status Cuti

Setiap pengajuan cuti memiliki alur status:

```text
Pending → Approved
        ↘ Rejected
```

Keterangan:

* Pending : Menunggu keputusan Admin
* Approved : Disetujui Admin
* Rejected : Ditolak Admin

Status hanya dapat diubah oleh Admin dan hanya dapat diproses apabila status saat ini masih `pending`.


