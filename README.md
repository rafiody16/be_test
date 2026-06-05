# 🚀 BE Test - Installation Guide

Aplikasi ini dibangun menggunakan **Laravel 11** dan membutuhkan **PHP >= 8.3**.

Proyek dapat dijalankan menggunakan:

- 🐳 Docker / Podman (Direkomendasikan)
- 💻 Laragon / XAMPP (Tanpa Docker)

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

## 4. Install Dependency

Jalankan Composer langsung dari host:

```bash
composer install
```

## 5. Jalankan Container

### Podman

```bash
podman compose up -d --build
```

### Docker

```bash
docker compose up -d --build
```

> ⚠️ Tunggu sekitar 15–30 detik saat pertama kali menjalankan container agar MySQL selesai melakukan inisialisasi.

## 6. Setup Laravel

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

## 7. Akses Aplikasi

Buka browser:

```text
http://localhost:8000
```

---

# 💻 Metode 2: Laragon / XAMPP

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

## 5. Install Dependency dan Setup Laravel

```bash
composer install

php artisan key:generate

php artisan migrate:fresh --seed

php artisan storage:link
```

## 6. Jalankan Aplikasi

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

# 🧰 Teknologi yang Digunakan

- Laravel 11
- PHP 8.3+
- MySQL
- Docker / Podman
- Composer

---

# 👨‍💻 Developer Setup Checklist

- [ ] Clone repository
- [ ] Copy `.env`
- [ ] Konfigurasi database
- [ ] Jalankan `composer install`
- [ ] Generate app key
- [ ] Jalankan migrasi & seeder
- [ ] Jalankan aplikasi
- [ ] Verifikasi aplikasi dapat diakses
