# Pelaporan Warga (Laravel + JWT)

UTS: Sistem Pelaporan Warga.

## Fitur

- REST API dengan authentication berbasis JWT (`tymon/jwt-auth`)
- CRUD laporan (warga) + tanggapan & update status (admin)
- UI Blade + Bootstrap 5 (Landing, Login/Register, Dashboard, Laporan)
- Frontend statis (Vanilla HTML + JS) yang mengonsumsi REST API + JWT (untuk ketentuan tugas)

## Struktur Database

### users

- `id`, `name`, `email`, `password`, `role` (`admin|warga`), timestamps

### laporans

- `id`, `user_id`, `judul`, `deskripsi`, `kategori`, `lokasi`, `no_hp`, `foto`, `status` (`menunggu|diproses|selesai|ditolak`), `kode_token` (unik), timestamps

### tanggapans

- `id`, `laporan_id`, `admin_id`, `isi_tanggapan`, timestamps

Relasi:

- User `hasMany` Laporan
- Laporan `belongsTo` User
- Laporan `hasMany` Tanggapan
- Tanggapan `belongsTo` Laporan
- Tanggapan `belongsTo` Admin(User)

## Setup (Local)

1) Install dependency

- `composer install`

2) Set database MySQL/MariaDB di `.env`

```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=pelaporan_warga
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASS
```

Catatan: environment ini tidak punya `pdo_sqlite`, jadi SQLite tidak bisa dipakai.

3) Jalankan migration

- `php artisan migrate`

4) Storage link (foto laporan)

- `php artisan storage:link`

## REST API (JWT)

Base URL: `http://localhost/api`

Semua endpoint laporan dilindungi middleware JWT.

### Auth

- `POST /api/register`
- `POST /api/login`
- `POST /api/logout` (JWT required)
- `POST /api/refresh` (JWT required)

#### Request: Register

Body (JSON):

```json
{
  "name": "Budi",
  "email": "budi@mail.com",
  "password": "secret123"
}
```

#### Request: Login

Body (JSON):

```json
{
  "email": "budi@mail.com",
  "password": "secret123"
}
```

Catatan: field `email` juga bisa diisi **username** (nilai `name`) untuk login.

#### Response: Login (contoh)

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "access_token": "<jwt>",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "Budi",
      "email": "budi@mail.com",
      "role": "warga",
      "created_at": "2026-03-09T10:00:00.000000Z",
      "updated_at": "2026-03-09T10:00:00.000000Z"
    }
  }
}
```

### Laporan (JWT required)

- `GET /api/laporans`
- `GET /api/laporans/{id}`
- `POST /api/laporans` (multipart untuk upload foto)
- `PUT /api/laporans/{id}` (multipart untuk upload foto)
- `DELETE /api/laporans/{id}`

Kategori:

- `sampah`, `banjir`, `jalan rusak`, `lampu jalan mati`

Status:

- `menunggu`, `diproses`, `selesai`, `ditolak`

Header JWT:

- `Authorization: Bearer <access_token>`

#### Request: Create Laporan (Postman)

- Method: `POST`
- URL: `{{base_url}}/api/laporans`
- Header: `Authorization: Bearer {{access_token}}`
- Body: `form-data`
  - `judul` (text)
  - `kategori` (text)
  - `lokasi` (text)
  - `deskripsi` (text)
  - `foto` (file, optional)

#### Response: Create Laporan (contoh)

```json
{
  "success": true,
  "message": "Laporan berhasil dibuat",
  "data": {
    "id": 10,
    "user_id": 1,
    "judul": "Sampah menumpuk",
    "deskripsi": "Sampah menumpuk di depan sekolah.",
    "kategori": "sampah",
    "lokasi": "Jl. Merdeka",
    "no_hp": "08xxxxxxxxxx",
    "foto": "laporans/xxxx.jpg",
    "foto_url": "/storage/laporans/xxxx.jpg",
    "status": "menunggu",
    "kode_token": "ABC123XYZ",
    "created_at": "2026-03-09T10:05:00.000000Z",
    "updated_at": "2026-03-09T10:05:00.000000Z"
  }
}
```

### Admin (JWT required + role admin)

- `PATCH /api/laporans/{id}/status`

Body (JSON):

```json
{ "status": "diproses" }
```

- `POST /api/laporans/{id}/tanggapans`

Body (JSON):

```json
{ "isi_tanggapan": "Sedang kami tindak lanjuti" }
```

## UI (Blade + Bootstrap 5)

- Landing: `/`
- Login: `/login`
- Register: `/register`
- Dashboard: `/dashboard` (redirect ke `/`)
- Daftar laporan: `/laporans`
- Laporan saya: `/laporans/saya`
- Admin panel: `/admin/panel` (khusus admin)

## Frontend (API Consumer + JWT)

Folder: `public/frontend`

Frontend ini dibuat untuk memenuhi ketentuan bahwa **frontend mengonsumsi REST API dan menggunakan JWT** (token disimpan di `localStorage` dan dikirim via header `Authorization: Bearer <token>`).

Halaman:

- Login: `/frontend/login.html`
- Register: `/frontend/register.html`
- Daftar laporan: `/frontend/laporans.html`
- Form create/edit laporan: `/frontend/laporan-form.html`

Cara akses:

- Jika menjalankan `php artisan serve`, buka `http://127.0.0.1:8000/frontend/login.html`
- Jika memakai web server (Apache/Nginx) dengan document root ke folder `public/`, buka `http://localhost/frontend/login.html`
