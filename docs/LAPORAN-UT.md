# Laporan Proyek UT — Pelaporan Warga (Laravel + JWT)

## 1. Identitas

- Nama: **[ISI NAMA]**
- NPM: **[ISI NPM]**
- Kelas: **[ISI KELAS]**
- Mata Kuliah: **[ISI MATA KULIAH]**
- Tanggal: **10 Maret 2026**

## 2. Ringkasan Aplikasi

**Pelaporan Warga** adalah aplikasi pelaporan masalah lingkungan/fasilitas umum berbasis web. Pengguna (warga) dapat membuat laporan (judul, kategori, lokasi, no HP, deskripsi, foto opsional), memantau status, serta menerima tanggapan dari admin. Aplikasi menyediakan:

- UI Web (Blade + Bootstrap 5) untuk kebutuhan aplikasi web.
- REST API (JSON) yang diamankan dengan JWT (library `tymon/jwt-auth`).
- Frontend statis (HTML + JS) yang mengonsumsi REST API dan menyimpan token JWT di `localStorage`.

### 2.1. Peran Pengguna

- **Warga**: registrasi, login, membuat laporan, melihat laporan, edit/hapus laporan milik sendiri.
- **Admin**: melihat semua laporan, mengubah status laporan, memberi tanggapan.

### 2.2. Kategori & Status

- **Kategori laporan**: `sampah`, `banjir`, `jalan rusak`, `lampu jalan mati`.
- **Status laporan**: `menunggu`, `diproses`, `selesai`, `ditolak`.

## 3. Screenshot Frontend (Lampiran Wajib)

Tambahkan screenshot berikut (ambil dari browser/HP lalu simpan ke folder `docs/screenshots/`).

Minimal yang disarankan:

1. **Landing / Dashboard**
2. **Halaman Login / Register**
3. **Daftar Laporan**
4. **Form Tambah / Edit Laporan**
5. **Admin Panel (jika ada akun admin)**

Template penyisipan gambar (ganti nama file sesuai screenshot kamu):

- Landing/Dashboard:

  ![Landing](screenshots/01-landing.png)

- Login:

  ![Login](screenshots/02-login.png)

- Register:

  ![Register](screenshots/03-register.png)

- Daftar Laporan (frontend statis konsumsi API):

  ![Daftar Laporan](screenshots/04-laporans.png)

- Form Laporan (create/edit):

  ![Form Laporan](screenshots/05-laporan-form.png)

## 4. Arsitektur & Struktur Data

### 4.1. Teknologi

- Backend: Laravel 8
- Auth API: JWT (`tymon/jwt-auth`)
- Database: MySQL/MariaDB
- UI Web: Blade + Bootstrap 5
- Frontend statis (API consumer): Vanilla HTML + JS

### 4.2. Tabel Database

**users**
- `id`, `name`, `email`, `password`, `role` (`admin|warga`), timestamps

**laporans**
- `id`, `user_id`, `judul`, `deskripsi`, `kategori`, `lokasi`, `no_hp`, `foto`, `status`, `kode_token` (unik), timestamps

**tanggapans**
- `id`, `laporan_id`, `admin_id`, `isi_tanggapan`, timestamps

### 4.3. Relasi (Eloquent)

- User `hasMany` Laporan
- Laporan `belongsTo` User
- Laporan `hasMany` Tanggapan
- Tanggapan `belongsTo` Laporan
- Tanggapan `belongsTo` Admin(User)

## 5. Fitur yang Dikerjakan

Catatan: yang dimaksud **daftar fitur** di laporan UT ini adalah **fitur aplikasi yang terlihat/dirasakan pengguna** (warga/admin) dan kemampuan layanan (API) yang dipakai frontend. Hal-hal seperti JWT middleware, FormRequest, struktur response, dll tetap dicantumkan di bagian **Best Practices**.

### 5.1. Fitur Web (Blade + Bootstrap) — Warga

- Registrasi akun warga
- Login/Logout (session)
- Buat laporan (judul, kategori, lokasi, no HP, deskripsi, foto opsional)
- Lihat daftar & detail laporan milik sendiri
- Edit/Hapus laporan milik sendiri
- Pantau status laporan (menunggu/diproses/selesai/ditolak)

### 5.2. Fitur Web (Blade + Bootstrap) — Admin

- Melihat semua laporan warga
- Mengubah status laporan
- Memberi tanggapan pada laporan

### 5.3. Fitur API (REST JSON + JWT)

- Autentikasi API (login) menghasilkan `access_token` JWT
- Endpoint laporan terlindungi JWT (butuh header `Authorization: Bearer <token>`)
- CRUD laporan via API sesuai hak akses (owner vs admin)
- Endpoint admin (status/tanggapan) dibatasi role admin

### 5.4. Frontend Statis Konsumsi API (JWT)

- Login/Logout memakai endpoint API dan menyimpan `access_token` ke `localStorage`.
- Semua request laporan mengirim header `Authorization: Bearer <token>`.
- Jika token invalid/expired (HTTP 401), sesi dibersihkan dan user dipaksa login ulang.

## 6. Best Practices yang Diterapkan (dengan bukti)

Berikut daftar best practices yang digunakan beserta bukti file/komponen.

1. **Validasi terstruktur menggunakan FormRequest**
   - Store: `LaporanStoreRequest`
   - Update: `LaporanUpdateRequest`
   - Update status: `LaporanStatusRequest`

2. **Middleware keamanan JWT untuk API**
   - Middleware `jwt.custom` mem-parse token dan set user ke guard `api`.

3. **Role-based access control (RBAC) untuk admin**
   - API: middleware `role.admin` membatasi endpoint status/tanggapan.
   - Web: middleware `role.admin.web` membatasi admin panel.

4. **Pengelolaan upload file via Storage disk `public`**
   - Foto laporan disimpan pada `storage/app/public/laporans` dan diakses via `storage:link`.

5. **Pemisahan Web vs API**
   - Rute web berada di `routes/web.php`.
   - Rute API berada di `routes/api.php`.

6. **Response API konsisten**
   - Struktur umum: `{ success, message, data }`.

Catatan bukti (kode): dosen biasanya minta potongan kode. Kamu bisa menyertakan 1–2 screenshot kode dari file berikut:
- Middleware JWT: `app/Http/Middleware/JwtMiddleware.php`
- Middleware admin API/web: `app/Http/Middleware/AdminMiddleware.php`, `app/Http/Middleware/AdminWebMiddleware.php`
- Validasi laporan: `app/Http/Requests/Api/LaporanStoreRequest.php`
- Konsumsi token di frontend: `public/frontend/js/api.js`

## 7. Daftar Endpoint & Format Response

Base URL API (lokal): `http://localhost:8000/api`

Disarankan menambahkan header berikut saat testing di Postman agar selalu menerima JSON:

- `Accept: application/json`

Header JWT untuk endpoint yang dilindungi:

- `Authorization: Bearer <access_token>`

### 7.0. Format Response API Konsisten

Semua response API (baik sukses maupun error) menggunakan struktur dasar:

```json
{
  "success": true,
  "message": "...",
  "data": {}
}
```

Catatan:

- Untuk response sukses, `success: true` dan `data` berisi object/array.
- Untuk response error, `success: false` dan `data` biasanya `null`.
- Khusus error validasi (HTTP 422), `data` berisi `errors`.

**Contoh error validasi (HTTP 422):**

```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": {
    "errors": {
      "judul": ["The judul field is required."],
      "no_hp": ["The no hp field is required."]
    }
  }
}
```

**Contoh unauthorized (HTTP 401) saat token tidak ada/invalid:**

```json
{
  "success": false,
  "message": "Unauthorized",
  "data": null
}
```

**Contoh forbidden (HTTP 403) saat role tidak sesuai:**

```json
{
  "success": false,
  "message": "Forbidden",
  "data": null
}
```

**Contoh not found (HTTP 404):**

```json
{
  "success": false,
  "message": "Not Found",
  "data": null
}
```

**Contoh method not allowed (HTTP 405):**

```json
{
  "success": false,
  "message": "Method Not Allowed",
  "data": null
}
```

### 7.1. Auth

| Method | Endpoint | Auth | Deskripsi |
|---|---|---:|---|
| POST | `/register` | Tidak | Registrasi warga |
| POST | `/login` | Tidak | Login (email/username + password) |
| POST | `/logout` | Ya | Logout |
| POST | `/refresh` | Ya | Refresh token |

**Contoh response login (ringkas):**

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
      "role": "warga"
    }
  }
}
```

### 7.2. Laporan (JWT required)

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/laporans` | List laporan |
| GET | `/laporans/{id}` | Detail laporan |
| POST | `/laporans` | Buat laporan (multipart, foto opsional) |
| PUT | `/laporans/{id}` | Update laporan (multipart, foto opsional) |
| DELETE | `/laporans/{id}` | Hapus laporan |

**Format response umum:**

```json
{
  "success": true,
  "message": "...",
  "data": {}
}
```

**Contoh response create laporan (ringkas):**

```json
{
  "success": true,
  "message": "Laporan berhasil dibuat",
  "data": {
    "id": 10,
    "user_id": 1,
    "judul": "Sampah menumpuk",
    "kategori": "sampah",
    "lokasi": "Jl. Merdeka",
    "no_hp": "08xxxxxxxxxx",
    "deskripsi": "Sampah menumpuk di depan sekolah.",
    "foto": "laporans/xxxx.jpg",
    "foto_url": "/storage/laporans/xxxx.jpg",
    "status": "menunggu",
    "kode_token": "ABCDEFGHIJ"
  }
}
```

### 7.3. Admin (JWT + role admin)

| Method | Endpoint | Deskripsi |
|---|---|---|
| PATCH | `/laporans/{id}/status` | Update status laporan |
| POST | `/laporans/{laporanId}/tanggapans` | Buat tanggapan admin |

**Request update status:**

```json
{ "status": "diproses" }
```

**Request create tanggapan:**

```json
{ "isi_tanggapan": "Sedang kami tindak lanjuti" }
```

## 8. Cara Menjalankan (Ringkas)

1. `composer install`
2. Atur `.env` untuk database MySQL/MariaDB
3. `php artisan migrate`
4. `php artisan storage:link`
5. Jalankan server: `php artisan serve --port=8000`
6. Akses:
   - Web (Blade): `http://127.0.0.1:8000/`
   - Frontend statis (API consumer): `http://127.0.0.1:8000/frontend/login.html`

## 9. Kesimpulan

Aplikasi Pelaporan Warga berhasil dibuat dengan dua antarmuka (Web Blade dan frontend statis) serta REST API yang aman dengan JWT. Fitur inti CRUD laporan dan fitur admin (status & tanggapan) sudah berjalan, disertai validasi input dan kontrol akses berbasis role.

---

### Checklist sebelum dikumpulkan

- [ ] Identitas sudah diisi lengkap
- [ ] Screenshot frontend sudah ditambahkan
- [ ] Minimal 1 screenshot kode (best practice) ditambahkan
- [ ] Endpoint + contoh response sudah sesuai hasil pengujian (Postman/Browser)
