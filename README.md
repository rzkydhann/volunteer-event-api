# 🌱 Volunteer Event Management API

REST API untuk sistem manajemen event volunteer, dibangun dengan **Laravel 12** + **MySQL** + **Laravel Sanctum**.

---

## 📋 Daftar Isi
- [Tech Stack](#tech-stack)
- [Persiapan Database MySQL](#persiapan-database-mysql)
- [Cara Install](#cara-install)
- [Cara Menjalankan](#cara-menjalankan)
- [Daftar Endpoint API](#daftar-endpoint-api)
- [Contoh Request & Response](#contoh-request--response)
- [Pertanyaan Wajib](#pertanyaan-wajib)
- [Catatan Desain](#catatan-desain)

---

## 🛠 Tech Stack

| Komponen   | Pilihan              |
|------------|---------------------|
| Framework  | Laravel 12           |
| PHP        | >= 8.2               |
| Database   | MySQL (phpMyAdmin)   |
| Auth       | Laravel Sanctum 4.x  |

---

## 🗄 Persiapan Database MySQL

Sebelum install project, buat database dulu di **phpMyAdmin**:

1. Buka phpMyAdmin → `http://localhost/phpmyadmin`
2. Klik **"New"** di panel kiri
3. Isi nama database: `volunteer_event`
4. Collation: `utf8mb4_unicode_ci`
5. Klik **"Create"**

> Tidak perlu buat tabel manual — Laravel akan buat otomatis via migration.

---

## ⚙️ Cara Install

### 1. Clone repository
```bash
git clone https://github.com/rzkydhann/volunteer-event-api.git
cd volunteer-event-api
```

### 2. Install dependencies
```bash
composer install
```

### 3. Salin file environment
```bash
cp .env.example .env
```

### 4. Konfigurasi `.env` — sesuaikan dengan MySQL kamu
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=volunteer_event
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate application key
```bash
php artisan key:generate
```

### 6. Install Laravel Sanctum
```bash
composer require laravel/sanctum
```

### 7. Jalankan migration
```bash
php artisan migrate
```

Cek phpMyAdmin — seharusnya sudah ada 4 tabel:
- `users`
- `personal_access_tokens`
- `events`
- `event_user`

### 8. (Opsional) Jalankan seeder untuk data dummy
```bash
php artisan db:seed
```

Data yang dibuat:
| Nama    | Email                   | Password    |
|---------|------------------------|-------------|
| Budi   | budi@example.com       | password123 |
| Rizky     | rizky@example.com         | password123 |
| Bagus | agus@example.com     | password123 |

---

## 🚀 Cara Menjalankan

```bash
php artisan serve
```

API tersedia di: **`http://volunteer-api.test/api/`**

> ⚠️ **Penting:** Selalu sertakan header `Accept: application/json` di setiap request agar Laravel selalu mengembalikan JSON, bukan HTML.

---

## 📡 Daftar Endpoint API

### Authentication

| Method | Endpoint        | Auth | Deskripsi           |
|--------|----------------|------|---------------------|
| POST   | `/api/register` | ❌   | Registrasi user baru |
| POST   | `/api/login`    | ❌   | Login                |
| POST   | `/api/logout`   | ✅   | Logout               |
| GET    | `/api/me`       | ✅   | Profil user aktif    |

### Events

| Method | Endpoint                  | Auth | Deskripsi             |
|--------|--------------------------|------|------------------------|
| GET    | `/api/events`             | ✅   | Daftar semua event     |
| POST   | `/api/events`             | ✅   | Buat event baru        |
| GET    | `/api/events/{id}`        | ✅   | Detail event           |
| POST   | `/api/events/{id}/join`   | ✅   | Join event             |

> ✅ = Wajib kirim header `Authorization: Bearer {token}`

---

## 📄 Contoh Request & Response

### POST /api/register
```json
// Request Body
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

// Response 201
{
  "success": true,
  "message": "Registrasi berhasil.",
  "data": {
    "user": { "id": 1, "name": "Budi Santoso", "email": "budi@example.com" },
    "token": "1|abc123xyz...",
    "token_type": "Bearer"
  }
}
```

### POST /api/login
```json
// Request Body
{ "email": "agus@example.com", "password": "password123" }

// Response 200
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "user": { "id": 1, "name": "Agus Setiawan", "email": "agus@example.com" },
    "token": "2|xyz789...",
    "token_type": "Bearer"
  }
}
```

### GET /api/events
```json
// Response 200
{
  "success": true,
  "message": "Daftar event berhasil diambil.",
  "data": [
    {
      "id": 1,
      "title": "Bersih-Bersih Pantai Ancol",
      "description": "Mari bersama menjaga kebersihan pantai...",
      "event_date": "2025-02-20 08:00:00",
      "creator": { "id": 1, "name": "Agus Setiawan", "email": "agus@example.com" },
      "total_participants": 2,
      "created_at": "2025-02-13 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 3
  }
}
```

### POST /api/events/{id}/join — Sukses
```json
{
  "success": true,
  "message": "Berhasil bergabung ke event.",
  "data": {
    "event_id": 1,
    "event_title": "Bersih-Bersih Pantai Ancol",
    "event_date": "2025-02-20 08:00:00",
    "user_id": 3,
    "user_name": "Rizky Ananda Ramadhan",
    "joined_at": "2025-02-13 11:30:00",
    "total_participants": 3
  }
}
```

### Error — Sudah join
```json
{ "success": false, "message": "Kamu sudah terdaftar di event ini.", "data": null }
```

### Error — Validasi (422)
```json
{
  "success": false,
  "message": "Data yang dikirim tidak valid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Error — Unauthenticated (401)
```json
{ "success": false, "message": "Unauthenticated. Silakan login terlebih dahulu.", "data": null }
```

---

## ❓ Pertanyaan Wajib

### 1. Bagian tersulit dari assignment ini?

Bagian yang paling membutuhkan perhatian adalah **konsistensi error handling** untuk seluruh endpoint. Secara default Laravel 12 mengembalikan HTML saat terjadi error (404, 401), bukan JSON. Di Laravel 12, override dilakukan langsung di `bootstrap/app.php` melalui `withExceptions()`, tidak lagi menggunakan `Handler.php` terpisah seperti versi sebelumnya — ini perlu pemahaman lebih tentang struktur baru Laravel 12.

Selain itu, logika `join` event juga perlu penanganan berlapis: validasi keberadaan event, cek creator, cek sudah join, dan cek tanggal event sudah lewat apa belum.

### 2. Jika diberi waktu 1 minggu, apa yang akan diperbaiki?

- **Policy & Authorization** — hanya creator yang bisa edit/hapus event miliknya
- **Fitur unjoin** — user bisa membatalkan keikutsertaan
- **Filter & Search** — filter event berdasarkan tanggal, status (upcoming/past), keyword
- **Rate Limiting** — batasi jumlah request per IP untuk mencegah abuse
- **Soft Delete** — event diarsipkan, tidak langsung dihapus permanen
- **Email Notifikasi** — reminder ke peserta mendekati tanggal event

### 3. Kenapa memilih pendekatan teknis ini?

- **Laravel Sanctum** dipilih karena sistemnya ringan dan tidak ribet. Sangat pas untuk aplikasi skala mahasiswa karena cara pakainya gampang: setelah login, kamu dapat "kunci" (token) yang bisa ditempel di Postman atau aplikasi HP buat akses fitur lainnya.
- **MySQL** dipilih karena sudah tersedia via phpMyAdmin di environment lokal (XAMPP/WAMP/Laragon), handal untuk data relasional, dan mendukung foreign key constraint yang dibutuhkan relasi many-to-many.
- **API Resource** (`EventResource`) Memusatkan transformasi data — ketika struktur response berubah, cukup edit di satu file.
- **Pagination** diterapkan sejak awal di `GET /events` supaya aplikasi tidak lemot kalau data event-nya sudah ribuan. Jadi, datanya tidak dikirim semua sekaligus, tapi dicicil per halaman (misal 10 data per halaman).
- **Pivot table `event_user`** adalah pendekatan standar Laravel untuk many-to-many, dengan unique constraint untuk mencegah duplikasi join.

---

## 🗂 Catatan Desain

### Struktur Folder
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php    # register, login, logout, me
│   │   └── EventController.php   # index, store, show, join
│   └── Resources/
│       └── EventResource.php     # format response event
├── Models/
│   ├── User.php                  # HasApiTokens + relasi
│   └── Event.php                 # relasi creator & participants
bootstrap/
└── app.php                       # Laravel 12: routing + middleware + error handling
config/
├── auth.php
├── database.php
└── sanctum.php
database/
├── migrations/                   # 4 migration file
└── seeders/
    └── DatabaseSeeder.php
routes/
└── api.php
```

### Asumsi Desain
1. Semua endpoint (kecuali register & login) membutuhkan autentikasi Bearer Token
2. Creator event tidak bisa join event miliknya sendiri
3. User tidak bisa join event yang tanggalnya sudah lewat
4. Satu user hanya bisa join satu event sekali (dijaga oleh unique constraint di database)
5. Semua response menggunakan format seragam: `{ success, message, data }`

### Skema Database MySQL
```
users
├── id           BIGINT UNSIGNED AUTO_INCREMENT PK
├── name         VARCHAR(255)
├── email        VARCHAR(255) UNIQUE
├── password     VARCHAR(255)
└── timestamps

events
├── id           BIGINT UNSIGNED AUTO_INCREMENT PK
├── title        VARCHAR(255)
├── description  TEXT
├── event_date   DATETIME
├── user_id      BIGINT UNSIGNED FK → users.id
└── timestamps

event_user (pivot)
├── id           BIGINT UNSIGNED AUTO_INCREMENT PK
├── event_id     BIGINT UNSIGNED FK → events.id
├── user_id      BIGINT UNSIGNED FK → users.id
├── UNIQUE       (event_id, user_id)
└── timestamps

personal_access_tokens (Sanctum)
├── id           BIGINT UNSIGNED AUTO_INCREMENT PK
├── tokenable_type + tokenable_id (polymorphic)
├── name         VARCHAR(255)
├── token        VARCHAR(64) UNIQUE
└── timestamps
```
