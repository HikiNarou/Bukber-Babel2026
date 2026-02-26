# YukBukber 🌙

YukBukber merupakan Website untuk membantu perencanaan acara buka bersama Ramadhan.

Proyek ini terdiri dari:

- **bukber-web**: Frontend Next.js untuk pendaftaran, dashboard, voting, dan informasi tanggal final.
- **bukber-api**: Backend Laravel untuk pengelolaan data peserta, lokasi, voting, dan kebutuhan admin.

## Fitur Utama

- Pendaftaran peserta bukber
- Usulan dan pemilihan lokasi
- Dashboard ringkasan data
- Voting lokasi favorit
- Penentuan tanggal final

## Struktur Repository

```text
YukBukber-Cosmic/
├── bukber-web/   # Next.js frontend
├── bukber-api/   # Laravel backend
└── readme.md
```

## Menjalankan Proyek (Singkat)

### 1) Frontend (bukber-web)

```bash
cd bukber-web
npm install
npm run dev
```

Aplikasi frontend berjalan di `http://localhost:3000`.

### 2) Backend (bukber-api)

```bash
cd bukber-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

API backend berjalan di `http://localhost:8000`.

## Catatan

- Frontend dan backend berjalan terpisah.
- Pastikan konfigurasi URL API di frontend sesuai environment lokal.

## Kontribusi

Silakan buat branch baru, lakukan perubahan seperlunya, lalu ajukan pull request.
