# 📖 Guidebook Fullstack Website "Buka Bersama" Ramadhan — Production-Ready

> **Target**: Website siap **live-production** untuk mengorganisir acara Buka Bersama (Bukber) di bulan Ramadhan. Peserta bisa mendaftar, memilih jadwal & lokasi, melihat dashboard data, melakukan voting, dan menentukan tanggal final.
> **Backend**: Laravel (PHP 8.2+) + MySQL 8.x
> **Frontend**: Next.js 14+ (App Router) + Tailwind CSS 3.x + TypeScript

---

## 0) Ringkasan Eksekutif

Kamu akan membangun platform web "Buka Bersama" Ramadhan dengan **5 pilar utama**:

1. **Registrasi & Pendaftaran Bukber**

   - Form pendaftaran: nama lengkap, pilih minggu Ramadhan, preferensi hari, batas budget per orang (IDR).
   - Validasi data real-time (frontend & backend).
   - Konfirmasi pendaftaran dengan halaman ucapan/bukti berhasil.
2. **Pemilihan Lokasi**

   - Pilih lokasi via **peta interaktif** (Leaflet / Google Maps) atau **kolom pencarian**.
   - Autocomplete lokasi, pin marker, dan simpan koordinat + alamat.
3. **Dashboard & Kesimpulan Bukber**

   - Statistik: total peserta, rata-rata budget, minggu terfavorit, hari paling banyak bisa.
   - Chart modern (bar/pie/donut) untuk visualisasi data.
   - Tabel responden + detail ketersediaan masing-masing.
4. **Voting**

   - Sistem voting untuk menentukan lokasi/restoran favorit.
   - Real-time update hasil voting.
   - Deadline voting configurable.
5. **Penentuan Tanggal Final**

   - Halaman khusus admin/publik untuk lock tanggal bukber berdasarkan data voting & ketersediaan.
   - Announcement page dengan detail final (tanggal, lokasi, jam, estimasi budget).

---

## 1) Scope Produk & Definisi "Production-Ready"

### Production-ready berarti:

- **Aman**: validasi input ketat, CSRF protection, rate-limit, sanitasi data, prepared statements.
- **Cepat**: caching query, CDN untuk static assets, pagination, lazy loading.
- **Stabil**: error handling konsisten, fallback UI, logging terstruktur.
- **Maintainable**: struktur folder clean, migration DB, seeder, environment terpisah (dev/staging/prod).
- **Responsif**: mobile-first, semua halaman optimal di HP, tablet, dan desktop.

### Non-goals (untuk fase ini)

- Sistem pembayaran / split bill otomatis → fase advanced.
- Chat real-time antar peserta → fase advanced.
- Push notification native → fase advanced (cukup in-app notification dulu).
- Multi-event management (banyak event bukber paralel) → fase advanced.

---

## 2) User Flow Utama (End-to-End)

### Flow Lengkap:

```
┌─────────────────────────────────────────────────────────────────────────┐
│  STEP 1: REGISTRASI/DAFTAR BUKBER                                       │
│  ───────────────────────────────────                                    │
│  User buka web → Landing page → Klik "Daftar Bukber"                    │
│  → Form: Nama Lengkap, Pilih Minggu (1/2/3/4), Preferensi Hari,         │
│    Batas Budget 10-500rb (IDR per orang)                                │
│  → Submit → Validasi                                                    │
│                                                                         │
│  STEP 2: PILIH LOKASI                                                   │
│  ─────────────────────                                                  │
│  User dialihkan ke halaman lokasi atau Rekomendasi tempat               |
│  → Opsi A: User menginput Nama tempat atau Restoran (kolom)             │
│  → Opsi B: Pilih lokasi lewat MAP (Kordinat) atau Input Alamat Manual   │
│  → Pilih lokasi → Konfirmasi                                            │
│                                                                         │
│  STEP 3: HALAMAN SELESAI / KONFIRMASI                                   │
│  ────────────────────────────────────                                   │
│  Tampilkan ucapan terima kasih + bukti pendaftaran                      │
│  → Ringkasan data pendaftaran (nama, minggu, hari, budget, lokasi)      │
│  → Share link / kode referensi (opsional)                               │
│  → CTA: "Lihat Dashboard" atau "Ajak Teman"                             │
│                                                                         │
│  STEP 4: DASHBOARD / KESIMPULAN BUKBER (Publik)                         │
│  ──────────────────────────────────────────────                         │
│  Widget: Total Peserta, Rata-rata Budget, Minggu Terfavorit             │
│  Chart: Hari paling banyak bisa (bar graph modern)                      │
│  Tabel: Daftar responden + detail ketersediaan                          |
│  CTA: Area "Siap menentukan tanggal?" berdasarkan data Hari dan Minggu  |
│                                                                         │
│  STEP 5: VOTING (Publik)                                                │
│  ───────────────────────                                                │
│  Vote lokasi/restoran favorit (berdasarkan data yang di-input user)     │
│  Lihat hasil voting secara real-time                                    │
│                                                                         │
│  STEP 6: PENENTUAN TANGGAL FINAL                                        │
│  ────────────────────────────────                                       │
│  Lock tanggal berdasarkan data → Announcement page                      │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 3) Feature Matrix (Basic vs Menengah)

### A) Basic (MVP — Launch Pertama)

**Registrasi & Pendaftaran**

- Form multi-step: data diri → jadwal → budget.
- Pilih minggu Ramadhan (Minggu ke-1, ke-2, ke-3, ke-4).
- Checkbox preferensi hari (Senin–Minggu, bisa pilih lebih dari 1).
- Input batas budget per orang (IDR), format currency otomatis.
- Validasi real-time (required, min/max budget, format nama).
- Proteksi duplikasi pendaftaran (cek nama & device fingerprint ketat).

**Pemilihan Lokasi**

- Muncul Notifikasi meminta Akses Lokasi atau GPS agar akurat.
- Peta interaktif (Leaflet.js + OpenStreetMap atau Map Free lainnya).
- Klik di peta untuk drop pin → ambil koordinat + alamat (reverse geocoding).
- Kolom pencarian lokasi dengan autocomplete (Nominatim / Google Places).
- Simpan: nama tempat, alamat lengkap, latitude, longitude.
- Tampilkan lokasi terpilih di mini-map preview.

**Halaman Konfirmasi (Selesai)**

- Ucapan terima kasih dengan animasi (confetti / Islamic pattern animation).
- Kartu ringkasan pendaftaran (nama, minggu, hari, budget, lokasi).
- Tombol "Bagikan ke WhatsApp" (pre-filled message + link).
- Tombol "Lihat Dashboard Bukber".

**Dashboard / Kesimpulan Bukber (Publik)**

- Widget statistik:
  - 🧑‍🤝‍🧑 Total Peserta (counter animasi).
  - 💰 Rata-rata Budget (formatted IDR).
  - 📅 Minggu Terfavorit (badge + jumlah peserta per minggu).
  - 📊 Hari Paling Banyak Bisa (bar chart / horizontal bar chart modern).
- Tabel responden: nama, minggu pilihan, hari tersedia, budget, lokasi usulan.
- Filter & search di tabel responden.
- Detail ketersediaan: modal/expand row per responden.

**Voting (Publik)**

- Daftar lokasi/restoran yang diusulkan peserta (auto-aggregate dari pendaftaran).
- Tombol vote per lokasi (1 orang = 1 vote, validasi via cookie/session).
- Progress bar real-time per lokasi.
- Countdown deadline voting (opsional).

**Penentuan Tanggal**

- Halaman untuk menampilkan tanggal final yang sudah di-lock.
- Detail: tanggal, hari, lokasi terpilih, jam, estimasi budget rata-rata.
- Desain announcement card yang menarik (Islamic-themed).
- Tombol share ke WhatsApp/media sosial.

**Infra Basic**

- Laravel 11 API + MySQL 8.
- Next.js 14 App Router + Tailwind CSS.
- Nginx reverse proxy + SSL (Let's Encrypt).
- Single VPS deployment.

---

### B) Menengah (V1 — Setelah MVP Stabil)

**Registrasi Enhanced**

- Tambah field "Catatan khusus" (textarea).
- Edit/update pendaftaran sebelum deadline.
- QR Code bukti pendaftaran.

**Dashboard Enhanced**

- Donut chart distribusi budget range.
- Heatmap ketersediaan (minggu × hari).

**Voting Enhanced**

- Multi-round voting (eliminasi lokasi terendah).
- Komentar per opsi voting.
- Vote dengan bobot (prioritas 1, 2, 3).

**Penentuan Tanggal Enhanced**

- Auto-suggest tanggal berdasarkan ketersediaan terbanyak.

**Admin Panel (Menengah)**

- Login admin sederhana (hardcoded / env-based credentials).
- Manage peserta: lihat, edit, hapus pendaftaran.
- Set deadline registrasi & voting.
- Lock/unlock tanggal final.
- Export seluruh data.

---

## 4) Peta Halaman (Routes)

### 4.1 Frontend Routes (Next.js App Router)

> Semua halaman bersifat **publik** (tidak ada auth untuk peserta). Admin panel adalah satu-satunya yang memerlukan login.

| Route          | Nama Halaman      | Deskripsi                               | Komponen Kunci                                     |
| -------------- | ----------------- | --------------------------------------- | -------------------------------------------------- |
| `/`          | Landing Page      | Halaman utama, hero section, CTA daftar | Hero, countdown Ramadhan, CTA button, info singkat |
| `/daftar`    | Registrasi Bukber | Form pendaftaran multi-step             | Stepper, form fields, validasi, submit             |
| `/lokasi`    | Pilih Lokasi      | Peta interaktif + pencarian             | Map component, search bar, pin marker              |
| `/selesai`   | Konfirmasi        | Ucapan + bukti pendaftaran              | Konfirmasi card, share buttons, CTA                |
| `/dashboard` | Kesimpulan Bukber | Dashboard data & statistik              | Stat widgets, charts, tabel responden              |
| `/voting`    | Voting Lokasi     | Vote lokasi favorit                     | Vote cards, progress bars, countdown               |
| `/tanggal`   | Penentuan Tanggal | Announcement tanggal final              | Announcement card, detail info, share              |

### 4.2 Admin Routes (Protected)

| Route               | Nama Halaman      | Deskripsi                    |
| ------------------- | ----------------- | ---------------------------- |
| `/admin/login`    | Admin Login       | Form login admin             |
| `/admin`          | Admin Dashboard   | Overview statistik           |
| `/admin/peserta`  | Kelola Peserta    | CRUD data peserta            |
| `/admin/lokasi`   | Kelola Lokasi     | Manage lokasi yang diusulkan |
| `/admin/voting`   | Kelola Voting     | Set deadline, reset, manage  |
| `/admin/tanggal`  | Set Tanggal Final | Lock tanggal + broadcast     |
| `/admin/settings` | Pengaturan        | Deadline, konfigurasi event  |

### 4.3 API Routes (Laravel)

| Method     | Endpoint                           | Deskripsi                                            |
| ---------- | ---------------------------------- | ---------------------------------------------------- |
| `POST`   | `/api/v1/registrasi`             | Submit pendaftaran baru                              |
| `GET`    | `/api/v1/registrasi`             | List semua pendaftaran                               |
| `GET`    | `/api/v1/registrasi/{id}`        | Detail satu pendaftaran                              |
| `PUT`    | `/api/v1/registrasi/{id}`        | Update pendaftaran (sebelum deadline)                |
| `DELETE` | `/api/v1/registrasi/{id}`        | Hapus pendaftaran (admin only)                       |
| `GET`    | `/api/v1/dashboard/stats`        | Statistik: total peserta, avg budget, minggu favorit |
| `GET`    | `/api/v1/dashboard/chart/hari`   | Data chart hari paling banyak bisa                   |
| `GET`    | `/api/v1/dashboard/chart/minggu` | Data chart distribusi minggu                         |
| `GET`    | `/api/v1/dashboard/chart/budget` | Data chart distribusi budget                         |
| `GET`    | `/api/v1/dashboard/responden`    | List responden + detail ketersediaan                 |
| `GET`    | `/api/v1/lokasi`                 | List semua lokasi yang diusulkan                     |
| `GET`    | `/api/v1/lokasi/search`          | Search lokasi (autocomplete)                         |
| `POST`   | `/api/v1/lokasi`                 | Tambah lokasi baru                                   |
| `GET`    | `/api/v1/voting`                 | List opsi voting + hasil sementara                   |
| `POST`   | `/api/v1/voting`                 | Submit vote                                          |
| `GET`    | `/api/v1/voting/hasil`           | Hasil voting final                                   |
| `GET`    | `/api/v1/tanggal`                | Get tanggal final (jika sudah di-lock)               |
| `POST`   | `/api/v1/admin/login`            | Login admin                                          |
| `POST`   | `/api/v1/admin/tanggal`          | Set/lock tanggal final                               |
| `PUT`    | `/api/v1/admin/settings`         | Update pengaturan event                              |
| `DELETE` | `/api/v1/admin/peserta/{id}`     | Hapus peserta                                        |
| `POST`   | `/api/v1/admin/broadcast`        | Broadcast notifikasi ke peserta                      |

---

## 5) UX/UI Guidelines (Tema "Ramadhan Bukber")

### 5.1 Design System & Tema Warna

**Color Palette (Ramadhan-themed)**

```
Primary:        #1B4332  (Hijau tua — Islamic green)
Primary Light:  #2D6A4F  (Hijau medium)
Primary Accent: #40916C  (Hijau cerah)
Secondary:      #D4A017  (Emas/Gold — premium feel)
Secondary Light:#F2D06B  (Kuning emas muda)
Background:     #0A1628  (Dark navy — nuansa malam)
Surface:        #132238  (Dark card surface)
Surface Light:  #1C3450  (Hover/active state)
Text Primary:   #F1F5F9  (Putih soft)
Text Secondary: #94A3B8  (Abu-abu terang)
Accent Red:     #E74C3C  (Untuk warning/error)
Accent Green:   #27AE60  (Untuk sukses)
```

**Typography**

- Heading: `Plus Jakarta Sans` (Google Fonts) — modern, clean.
- Body: `Inter` (Google Fonts) — highly readable.
- Decorative (opsional): `Amiri` untuk aksen Arabik pada heading tertentu.

**Border Radius**: `12px` (cards), `8px` (buttons), `16px` (modals).

### 5.2 Layout Global

- **Header sticky**: Logo "Bukber Ramadhan 🌙", nav links (Daftar, Dashboard, Voting, Tanggal), hamburger menu (mobile).
- **Footer**: info event, credits, social links, Islamic pattern border.
- **Responsive**: mobile-first, form jadi full-width, chart stack vertical, tabel jadi card list di mobile.

### 5.3 Komponen UI Kustom

- `StatCard` — Widget statistik (icon + label + value + trend indicator).
- `StepperForm` — Multi-step form dengan progress indicator.
- `MapPicker` — Peta interaktif dengan pin & search overlay.
- `VoteCard` — Kartu voting per lokasi (foto, nama, vote count, progress bar).
- `RespondenTable` — Tabel data responden dengan filter, sort, dan expand row.
- `ChartWidget` — Wrapper untuk chart (bar, donut, heatmap).
- `AnnouncementCard` — Kartu pengumuman tanggal final (design premium).
- `ConfettiOverlay` — Animasi confetti untuk halaman konfirmasi.
- `CountdownTimer` — Countdown ke hari-H bukber / deadline voting.
- `BudgetSlider` — Custom range slider untuk input budget.
- `WeekSelector` — Visual selector untuk pilih minggu Ramadhan.
- `DayChips` — Chip/toggle group untuk pilih hari.
- `IslamicPatternBorder` — Dekoratif border bermotif Islamic geometry.
- `MoonPhaseIcon` — Icon bulan sabit animasi.

### 5.4 State & Error Handling (Konsisten)

- **Loading**: skeleton loader per section (bukan spinner global).
- **Empty state**: ilustrasi + CTA (mis. "Belum ada peserta, jadi yang pertama!").
- **Error state**: pesan error + tombol retry + fallback.
- **Success state**: animasi checkmark + confetti.
- **Form validation**: inline error messages (merah) + success indicators (hijau).

### 5.5 Aksesibilitas

- Semua form fields punya label yang jelas.
- Focus ring visible pada keyboard navigation.
- Color contrast ratio minimal 4.5:1.
- Map punya alternatif text-based location input.
- Aria labels pada icon-only buttons.

### 5.6 Animasi & Micro-interactions

- Hover scale pada cards (`transform: scale(1.02)`).
- Smooth page transitions (fade / slide).
- Counter animation pada stat widgets (number ticker).
- Progress bar animation pada voting.
- Confetti burst pada halaman konfirmasi.
- Floating lantern / crescent moon animation pada hero section.
- Chart entry animation (bars grow from 0).

---

## 6) Arsitektur Teknis (High-Level)

### 6.1 Komponen Sistem

**Frontend (Next.js)**

- Next.js 14+ App Router + TypeScript.
- Tailwind CSS 3.x untuk styling.
- Static & SSR rendering (hybrid).
- Client-side interactivity untuk map, charts, forms.

**Backend (Laravel)**

- Laravel 11 sebagai REST API.
- Eloquent ORM + MySQL 8.
- Laravel Sanctum untuk auth admin (token-based).
- Laravel Queue untuk background jobs (email broadcast, data aggregation).
- Laravel Scheduler untuk cron jobs (reminder, deadline).

**Database**

- MySQL 8.x (InnoDB).
- Indexed queries untuk aggregation statistik.

**Infra**

- Nginx reverse proxy (TLS termination).
- VPS (single server untuk MVP).
- Optional: Redis untuk caching & queue driver.
- Optional: Mailgun/SMTP untuk email.

### 6.2 Diagram Arsitektur

```
┌──────────────────┐
│   Browser/Client │
│   (Next.js SSR   │
│    + CSR)        │
└────────┬─────────┘
         │ HTTPS
         ▼
┌──────────────────┐         ┌─────────────────┐
│  NGINX Reverse   │────────▶│  CDN (Optional)  │
│  Proxy + SSL     │         │  Static Assets   │
└────────┬─────────┘         └─────────────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌────────┐ ┌──────────┐
│Next.js │ │ Laravel  │──────▶ MySQL 8.x
│ :3000  │ │ API :8000│──────▶ Redis (opsional)
└────────┘ └──────────┘
                │
                ▼
          ┌───────────┐
          │ Queue     │──▶ Email Service
          │ Worker    │──▶ Data Aggregation
          └───────────┘
```

### 6.3 Komunikasi Frontend ↔ Backend

- Frontend fetch data via REST API (`/api/v1/...`).
- Format response: JSON.
- CORS configured di Laravel untuk domain Next.js.
- Error format konsisten:

```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "nama_lengkap": ["Nama lengkap wajib diisi"],
    "budget": ["Budget minimal Rp 10.000"]
  }
}
```

- Success format:

```json
{
  "success": true,
  "message": "Pendaftaran berhasil",
  "data": {
    "id": 1,
    "nama_lengkap": "Ahmad Fauzi",
    "minggu": 2,
    "created_at": "2026-03-05T10:00:00Z"
  }
}
```

---

## 7) Database Design (MySQL)

### 7.1 Entity Relationship

```
peserta ──┬──▶ peserta_hari (pivot: hari yang bisa)
          ├──▶ peserta_lokasi (lokasi yang diusulkan)
          └──▶ votes (vote lokasi favorit)

lokasi ◀──── peserta_lokasi
       ◀──── votes

event_settings (singleton: konfigurasi event)
tanggal_final  (singleton: tanggal yang di-lock)
admin_users    (admin credentials)
```

### 7.2 Skema Tabel

**`peserta`** — Data pendaftaran peserta

| Kolom                  | Tipe                    | Keterangan            |
| ---------------------- | ----------------------- | --------------------- |
| `id`                 | BIGINT UNSIGNED, PK, AI | Primary key           |
| `uuid`               | CHAR(36), UNIQUE        | Public identifier     |
| `nama_lengkap`       | VARCHAR(100), NOT NULL  | Nama peserta          |
| `minggu`             | TINYINT(1), NOT NULL    | Minggu Ramadhan (1-4) |
| `budget_per_orang`   | INT UNSIGNED, NOT NULL  | Budget dalam Rupiah   |
| `catatan`            | TEXT, NULLABLE          | Catatan khusus        |
| `device_fingerprint` | VARCHAR(64), NULLABLE   | Anti-duplikasi        |
| `ip_address`         | VARCHAR(45), NULLABLE   | IPv4/IPv6             |
| `created_at`         | TIMESTAMP               |                       |
| `updated_at`         | TIMESTAMP               |                       |

**`peserta_hari`** — Hari yang bisa (pivot)

| Kolom          | Tipe                                                           | Keterangan |
| -------------- | -------------------------------------------------------------- | ---------- |
| `id`         | BIGINT UNSIGNED, PK, AI                                        |            |
| `peserta_id` | BIGINT UNSIGNED, FK → peserta.id                              |            |
| `hari`       | ENUM('senin','selasa','rabu','kamis','jumat','sabtu','minggu') |            |

**`lokasi`** — Lokasi yang diusulkan

| Kolom               | Tipe                              | Keterangan              |
| ------------------- | --------------------------------- | ----------------------- |
| `id`              | BIGINT UNSIGNED, PK, AI           |                         |
| `peserta_id`      | BIGINT UNSIGNED, FK → peserta.id | Siapa yang usulkan      |
| `nama_tempat`     | VARCHAR(200), NOT NULL            | Nama restoran/tempat    |
| `alamat`          | TEXT, NULLABLE                    | Alamat lengkap          |
| `latitude`        | DECIMAL(10,8), NULLABLE           | Koordinat               |
| `longitude`       | DECIMAL(11,8), NULLABLE           | Koordinat               |
| `google_place_id` | VARCHAR(100), NULLABLE            | Google Places reference |
| `created_at`      | TIMESTAMP                         |                         |

**`votes`** — Voting lokasi

| Kolom             | Tipe                             | Keterangan          |
| ----------------- | -------------------------------- | ------------------- |
| `id`            | BIGINT UNSIGNED, PK, AI          |                     |
| `lokasi_id`     | BIGINT UNSIGNED, FK → lokasi.id | Lokasi yang di-vote |
| `voter_name`    | VARCHAR(100), NOT NULL           | Nama voter          |
| `voter_ip`      | VARCHAR(45), NULLABLE            | Anti-duplikasi      |
| `session_token` | VARCHAR(64), NULLABLE            | Anti-duplikasi      |
| `created_at`    | TIMESTAMP                        |                     |

**`event_settings`** — Pengaturan event (singleton row)

| Kolom                    | Tipe                   | Keterangan               |
| ------------------------ | ---------------------- | ------------------------ |
| `id`                   | BIGINT UNSIGNED, PK    | Selalu 1                 |
| `nama_event`           | VARCHAR(200)           | "Bukber Magang BSB 2026" |
| `deadline_registrasi`  | DATETIME, NULLABLE     | Batas pendaftaran        |
| `deadline_voting`      | DATETIME, NULLABLE     | Batas voting             |
| `is_registration_open` | BOOLEAN, DEFAULT TRUE  |                          |
| `is_voting_open`       | BOOLEAN, DEFAULT FALSE |                          |
| `updated_at`           | TIMESTAMP              |                          |

**`tanggal_final`** — Tanggal final bukber (singleton)

| Kolom          | Tipe                             | Keterangan      |
| -------------- | -------------------------------- | --------------- |
| `id`         | BIGINT UNSIGNED, PK              | Selalu 1        |
| `tanggal`    | DATE, NOT NULL                   | Tanggal bukber  |
| `jam`        | TIME, NULLABLE                   | Jam kumpul      |
| `lokasi_id`  | BIGINT UNSIGNED, FK → lokasi.id | Lokasi terpilih |
| `catatan`    | TEXT, NULLABLE                   | Info tambahan   |
| `is_locked`  | BOOLEAN, DEFAULT FALSE           |                 |
| `created_at` | TIMESTAMP                        |                 |
| `updated_at` | TIMESTAMP                        |                 |

**`admin_users`** — Akun admin

| Kolom          | Tipe                    | Keterangan    |
| -------------- | ----------------------- | ------------- |
| `id`         | BIGINT UNSIGNED, PK, AI |               |
| `username`   | VARCHAR(50), UNIQUE     |               |
| `password`   | VARCHAR(255)            | Hash (bcrypt) |
| `created_at` | TIMESTAMP               |               |

### 7.3 Index yang Diperlukan

```sql
-- Peserta
CREATE INDEX idx_peserta_minggu ON peserta(minggu);
CREATE INDEX idx_peserta_created ON peserta(created_at);
CREATE INDEX idx_peserta_device ON peserta(device_fingerprint);

-- Peserta Hari (untuk aggregation)
CREATE INDEX idx_peserta_hari_hari ON peserta_hari(hari);
CREATE INDEX idx_peserta_hari_peserta ON peserta_hari(peserta_id);

-- Lokasi
CREATE INDEX idx_lokasi_peserta ON lokasi(peserta_id);

-- Votes
CREATE INDEX idx_votes_lokasi ON votes(lokasi_id);
CREATE INDEX idx_votes_session ON votes(session_token);
CREATE UNIQUE INDEX idx_votes_unique ON votes(lokasi_id, session_token);
```

---

## 8) API Design (Laravel) — Kontrak untuk Frontend

### 8.1 Prinsip Umum

- Base URL: `/api/v1/`
- Semua response JSON dengan struktur konsisten.
- Pagination: page-based (`?page=1&per_page=20`).
- Validasi di Form Request classes.
- Rate limiting: 60 requests/menit untuk public, 30/menit untuk submit.
- CORS: whitelist domain frontend.

### 8.2 Endpoint Detail

#### Registrasi

**`POST /api/v1/registrasi`** — Daftar peserta baru

Request Body:

```json
{
  "nama_lengkap": "Ahmad Fauzi",
  "minggu": 2,
  "hari": ["senin", "rabu", "jumat"],
  "budget_per_orang": 50000,
  "catatan": "Tidak bisa makan pedas",
  "lokasi": {
    "nama_tempat": "Warung Sate Pak Haji",
    "alamat": "Jl. Merdeka No. 10, Jakarta Selatan",
    "latitude": -6.2088,
    "longitude": 106.8456
  }
}
```

Response (201 Created):

```json
{
  "success": true,
  "message": "Pendaftaran berhasil! Jazakallahu khairan 🌙",
  "data": {
    "id": 15,
    "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "nama_lengkap": "Ahmad Fauzi",
    "minggu": 2,
    "hari": ["senin", "rabu", "jumat"],
    "budget_per_orang": 50000,
    "lokasi": {
      "nama_tempat": "Warung Sate Pak Haji",
      "alamat": "Jl. Merdeka No. 10, Jakarta Selatan"
    },
    "created_at": "2026-03-05T10:00:00Z"
  }
}
```

Validation Rules:

```php
[
    'nama_lengkap'    => 'required|string|min:3|max:100',
    'minggu'          => 'required|integer|between:1,4',
    'hari'            => 'required|array|min:1',
    'hari.*'          => 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
    'budget_per_orang'=> 'required|integer|min:10000|max:1000000',
    'catatan'         => 'nullable|string|max:500',
    'lokasi'          => 'required|array',
    'lokasi.nama_tempat' => 'required|string|max:200',
    'lokasi.alamat'   => 'nullable|string|max:500',
    'lokasi.latitude' => 'nullable|numeric|between:-90,90',
    'lokasi.longitude'=> 'nullable|numeric|between:-180,180',
]
```

**`GET /api/v1/registrasi`** — List semua peserta

Response:

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "uuid": "...",
      "nama_lengkap": "Ahmad Fauzi",
      "minggu": 2,
      "hari": ["senin", "rabu", "jumat"],
      "budget_per_orang": 50000,
      "lokasi": { "nama_tempat": "Warung Sate Pak Haji" },
      "created_at": "2026-03-05T10:00:00Z"
    }
  ],
  "meta": {
    "total": 25,
    "page": 1,
    "per_page": 20,
    "last_page": 2
  }
}
```

#### Dashboard / Statistik

**`GET /api/v1/dashboard/stats`** — Ringkasan statistik

Response:

```json
{
  "success": true,
  "data": {
    "total_peserta": 25,
    "rata_rata_budget": 55000,
    "min_budget": 20000,
    "max_budget": 100000,
    "minggu_terfavorit": {
      "minggu": 2,
      "jumlah_peserta": 12
    },
    "distribusi_minggu": [
      { "minggu": 1, "jumlah": 5 },
      { "minggu": 2, "jumlah": 12 },
      { "minggu": 3, "jumlah": 6 },
      { "minggu": 4, "jumlah": 2 }
    ]
  }
}
```

**`GET /api/v1/dashboard/chart/hari`** — Data chart hari

Response:

```json
{
  "success": true,
  "data": [
    { "hari": "senin", "jumlah": 15 },
    { "hari": "selasa", "jumlah": 10 },
    { "hari": "rabu", "jumlah": 18 },
    { "hari": "kamis", "jumlah": 20 },
    { "hari": "jumat", "jumlah": 22 },
    { "hari": "sabtu", "jumlah": 24 },
    { "hari": "minggu", "jumlah": 14 }
  ]
}
```

#### Voting

**`GET /api/v1/voting`** — List opsi voting

Response:

```json
{
  "success": true,
  "data": {
    "is_voting_open": true,
    "deadline": "2026-03-15T23:59:59Z",
    "lokasi": [
      {
        "id": 1,
        "nama_tempat": "Warung Sate Pak Haji",
        "alamat": "Jl. Merdeka No. 10",
        "total_votes": 8,
        "percentage": 32.0
      },
      {
        "id": 2,
        "nama_tempat": "Resto Padang Sederhana",
        "alamat": "Jl. Sudirman No. 5",
        "total_votes": 12,
        "percentage": 48.0
      }
    ],
    "total_voters": 25
  }
}
```

**`POST /api/v1/voting`** — Submit vote

Request:

```json
{
  "lokasi_id": 2,
  "voter_name": "Ahmad Fauzi"
}
```

#### Tanggal Final

**`GET /api/v1/tanggal`** — Get tanggal final

Response:

```json
{
  "success": true,
  "data": {
    "is_locked": true,
    "tanggal": "2026-03-20",
    "hari": "Jumat",
    "jam": "18:00",
    "lokasi": {
      "nama_tempat": "Resto Padang Sederhana",
      "alamat": "Jl. Sudirman No. 5, Jakarta Pusat"
    },
    "estimasi_budget": 55000,
    "total_peserta": 25,
    "catatan": "Kumpul jam 17:30 di lobby. Dress code: batik casual."
  }
}
```

---

## 9) Backend (Laravel) — Struktur Proyek & Best Practices

### 9.1 Tech Stack Backend

| Komponen         | Teknologi          | Versi                 |
| ---------------- | ------------------ | --------------------- |
| Framework        | Laravel            | 11.x                  |
| PHP              | PHP                | 8.2+                  |
| Database         | MySQL              | 8.x                   |
| Cache (opsional) | Redis              | 7.x                   |
| Queue            | Laravel Queue      | Database/Redis driver |
| Auth Admin       | Laravel Sanctum    | Token-based           |
| Validation       | Form Request       | Built-in Laravel      |
| Migration        | Laravel Migrations | Built-in              |
| Seeder           | Laravel Seeder     | Built-in              |
| Testing          | PHPUnit / Pest     | Built-in              |
| API Docs         | Scribe / Swagger   | Opsional              |

### 9.2 Struktur Folder Laravel

```
bukber-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── RegistrasiController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── LokasiController.php
│   │   │   │   ├── VotingController.php
│   │   │   │   ├── TanggalController.php
│   │   │   │   └── Admin/
│   │   │   │       ├── AuthController.php
│   │   │   │       ├── PesertaController.php
│   │   │   │       ├── SettingsController.php
│   │   │   │       └── TanggalController.php
│   │   ├── Requests/
│   │   │   ├── StoreRegistrasiRequest.php
│   │   │   ├── UpdateRegistrasiRequest.php
│   │   │   ├── StoreVoteRequest.php
│   │   │   └── Admin/
│   │   │       ├── LoginRequest.php
│   │   │       └── StoreTanggalRequest.php
│   │   ├── Resources/
│   │   │   ├── PesertaResource.php
│   │   │   ├── PesertaCollection.php
│   │   │   ├── LokasiResource.php
│   │   │   ├── VotingResource.php
│   │   │   ├── DashboardStatsResource.php
│   │   │   └── TanggalFinalResource.php
│   │   └── Middleware/
│   │       ├── AdminAuth.php
│   │       └── ThrottleRegistrasi.php
│   ├── Models/
│   │   ├── Peserta.php
│   │   ├── PesertaHari.php
│   │   ├── Lokasi.php
│   │   ├── Vote.php
│   │   ├── EventSetting.php
│   │   ├── TanggalFinal.php
│   │   └── AdminUser.php
│   ├── Services/
│   │   ├── RegistrasiService.php
│   │   ├── DashboardService.php
│   │   ├── VotingService.php
│   │   └── BroadcastService.php
│   ├── Enums/
│   │   ├── Hari.php
│   │   └── Minggu.php
│   └── Traits/
│       └── ApiResponse.php
├── database/
│   ├── migrations/
│   │   ├── 2026_01_01_000001_create_peserta_table.php
│   │   ├── 2026_01_01_000002_create_peserta_hari_table.php
│   │   ├── 2026_01_01_000003_create_lokasi_table.php
│   │   ├── 2026_01_01_000004_create_votes_table.php
│   │   ├── 2026_01_01_000005_create_event_settings_table.php
│   │   ├── 2026_01_01_000006_create_tanggal_final_table.php
│   │   └── 2026_01_01_000007_create_admin_users_table.php
│   └── seeders/
│       ├── AdminSeeder.php
│       ├── EventSettingSeeder.php
│       └── DummyPesertaSeeder.php
├── routes/
│   └── api.php
├── config/
├── tests/
│   ├── Feature/
│   │   ├── RegistrasiTest.php
│   │   ├── DashboardTest.php
│   │   ├── VotingTest.php
│   │   └── AdminTest.php
│   └── Unit/
│       ├── DashboardServiceTest.php
│       └── VotingServiceTest.php
└── .env.example
```

### 9.3 Contoh Route Definition (`routes/api.php`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrasiController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\VotingController;
use App\Http\Controllers\Api\TanggalController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\PesertaController as AdminPesertaController;
use App\Http\Controllers\Api\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Api\Admin\TanggalController as AdminTanggalController;

// ─── Public Routes ───────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Registrasi
    Route::post('/registrasi', [RegistrasiController::class, 'store'])
        ->middleware('throttle:registrasi');
    Route::get('/registrasi', [RegistrasiController::class, 'index']);
    Route::get('/registrasi/{uuid}', [RegistrasiController::class, 'show']);
    Route::put('/registrasi/{uuid}', [RegistrasiController::class, 'update']);

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/chart/hari', [DashboardController::class, 'chartHari']);
        Route::get('/chart/minggu', [DashboardController::class, 'chartMinggu']);
        Route::get('/chart/budget', [DashboardController::class, 'chartBudget']);
        Route::get('/responden', [DashboardController::class, 'responden']);
    });

    // Lokasi
    Route::get('/lokasi', [LokasiController::class, 'index']);
    Route::get('/lokasi/search', [LokasiController::class, 'search']);
    Route::post('/lokasi', [LokasiController::class, 'store']);

    // Voting
    Route::get('/voting', [VotingController::class, 'index']);
    Route::post('/voting', [VotingController::class, 'store'])
        ->middleware('throttle:voting');
    Route::get('/voting/hasil', [VotingController::class, 'hasil']);

    // Tanggal Final
    Route::get('/tanggal', [TanggalController::class, 'show']);

    // ─── Admin Routes ────────────────────────────────────────────
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/peserta', [AdminPesertaController::class, 'index']);
            Route::delete('/peserta/{id}', [AdminPesertaController::class, 'destroy']);
            Route::put('/settings', [AdminSettingsController::class, 'update']);
            Route::post('/tanggal', [AdminTanggalController::class, 'store']);
            Route::put('/tanggal', [AdminTanggalController::class, 'update']);
            Route::post('/broadcast', [AdminSettingsController::class, 'broadcast']);
        });
    });
});
```

### 9.4 Contoh Model Eloquent (`Peserta.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Peserta extends Model
{
    protected $table = 'peserta';

    protected $fillable = [
        'uuid', 'nama_lengkap', 'minggu',
        'budget_per_orang', 'catatan',
        'device_fingerprint', 'ip_address',
    ];

    protected $casts = [
        'minggu' => 'integer',
        'budget_per_orang' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Str::uuid()->toString();
        });
    }

    public function hari(): HasMany
    {
        return $this->hasMany(PesertaHari::class);
    }

    public function lokasi(): HasOne
    {
        return $this->hasOne(Lokasi::class);
    }

    // Scope: filter by minggu
    public function scopeMinggu($query, int $minggu)
    {
        return $query->where('minggu', $minggu);
    }
}
```

### 9.5 Contoh Service Layer (`DashboardService.php`)

```php
<?php

namespace App\Services;

use App\Models\Peserta;
use App\Models\PesertaHari;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('dashboard_stats', 60, function () {
            $peserta = Peserta::query();

            $totalPeserta = $peserta->count();
            $avgBudget = (int) $peserta->avg('budget_per_orang');
            $minBudget = (int) $peserta->min('budget_per_orang');
            $maxBudget = (int) $peserta->max('budget_per_orang');

            // Minggu terfavorit
            $distribusiMinggu = Peserta::select('minggu', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('minggu')
                ->orderByDesc('jumlah')
                ->get();

            $mingguFavorit = $distribusiMinggu->first();

            return [
                'total_peserta' => $totalPeserta,
                'rata_rata_budget' => $avgBudget,
                'min_budget' => $minBudget,
                'max_budget' => $maxBudget,
                'minggu_terfavorit' => $mingguFavorit ? [
                    'minggu' => $mingguFavorit->minggu,
                    'jumlah_peserta' => $mingguFavorit->jumlah,
                ] : null,
                'distribusi_minggu' => $distribusiMinggu->map(fn ($item) => [
                    'minggu' => $item->minggu,
                    'jumlah' => $item->jumlah,
                ]),
            ];
        });
    }

    public function getChartHari(): array
    {
        return Cache::remember('chart_hari', 60, function () {
            return PesertaHari::select('hari', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('hari')
                ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                ->get()
                ->toArray();
        });
    }

    public function getChartBudget(): array
    {
        return Cache::remember('chart_budget', 60, function () {
            return [
                'ranges' => [
                    ['label' => '< 25rb', 'jumlah' => Peserta::where('budget_per_orang', '<', 25000)->count()],
                    ['label' => '25rb - 50rb', 'jumlah' => Peserta::whereBetween('budget_per_orang', [25000, 50000])->count()],
                    ['label' => '50rb - 75rb', 'jumlah' => Peserta::whereBetween('budget_per_orang', [50001, 75000])->count()],
                    ['label' => '75rb - 100rb', 'jumlah' => Peserta::whereBetween('budget_per_orang', [75001, 100000])->count()],
                    ['label' => '> 100rb', 'jumlah' => Peserta::where('budget_per_orang', '>', 100000)->count()],
                ],
            ];
        });
    }
}
```

### 9.6 Trait API Response

```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function paginated($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'total' => $data->total(),
                'page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'last_page' => $data->lastPage(),
            ],
        ]);
    }
}
```

---

## 10) Frontend (Next.js + Tailwind) — Implementasi Production-Friendly

### 10.1 Tech Stack Frontend

| Komponen    | Teknologi                                           |
| ----------- | --------------------------------------------------- |
| Framework   | Next.js 14+ (App Router)                            |
| Language    | TypeScript                                          |
| Styling     | Tailwind CSS 3.x                                    |
| Charts      | Recharts / Chart.js                                 |
| Maps        | Leaflet.js + react-leaflet / @react-google-maps/api |
| Forms       | React Hook Form + Zod                               |
| HTTP Client | Axios / fetch wrapper                               |
| State       | Zustand (UI state)                                  |
| Animations  | Framer Motion                                       |
| Icons       | Lucide React / Heroicons                            |
| Fonts       | Google Fonts (Plus Jakarta Sans, Inter)             |

### 10.2 Folder Structure (Next.js App Router)

```
bukber-web/
├── app/
│   ├── layout.tsx              # Root layout (header, footer, fonts)
│   ├── page.tsx                # Landing page (/)
│   ├── daftar/
│   │   └── page.tsx            # Form registrasi (/daftar)
│   ├── lokasi/
│   │   └── page.tsx            # Pilih lokasi via map (/lokasi)
│   ├── selesai/
│   │   └── page.tsx            # Konfirmasi sukses (/selesai)
│   ├── dashboard/
│   │   └── page.tsx            # Dashboard statistik (/dashboard)
│   ├── voting/
│   │   └── page.tsx            # Voting lokasi (/voting)
│   ├── tanggal/
│   │   └── page.tsx            # Penentuan tanggal final (/tanggal)
│   ├── admin/
│   │   ├── login/
│   │   │   └── page.tsx        # Admin login
│   │   ├── page.tsx            # Admin dashboard
│   │   ├── peserta/
│   │   │   └── page.tsx        # Kelola peserta
│   │   ├── voting/
│   │   │   └── page.tsx        # Kelola voting
│   │   ├── tanggal/
│   │   │   └── page.tsx        # Set tanggal final
│   │   └── settings/
│   │       └── page.tsx        # Pengaturan event
│   ├── globals.css             # Tailwind base + custom styles
│   └── not-found.tsx           # 404 page
├── components/
│   ├── layout/
│   │   ├── Header.tsx
│   │   ├── Footer.tsx
│   │   ├── MobileNav.tsx
│   │   └── Container.tsx
│   ├── ui/
│   │   ├── Button.tsx
│   │   ├── Input.tsx
│   │   ├── Select.tsx
│   │   ├── Card.tsx
│   │   ├── Badge.tsx
│   │   ├── Modal.tsx
│   │   ├── Skeleton.tsx
│   │   ├── Toast.tsx
│   │   └── Spinner.tsx
│   ├── forms/
│   │   ├── StepperForm.tsx
│   │   ├── WeekSelector.tsx
│   │   ├── DayChips.tsx
│   │   ├── BudgetSlider.tsx
│   │   └── LocationSearch.tsx
│   ├── dashboard/
│   │   ├── StatCard.tsx
│   │   ├── HariChart.tsx
│   │   ├── MingguChart.tsx
│   │   ├── BudgetChart.tsx
│   │   ├── RespondenTable.tsx
│   │   └── KetersediaanDetail.tsx
│   ├── map/
│   │   ├── MapPicker.tsx
│   │   ├── MapPreview.tsx
│   │   └── LocationPin.tsx
│   ├── voting/
│   │   ├── VoteCard.tsx
│   │   ├── VoteProgress.tsx
│   │   └── VoteCountdown.tsx
│   ├── tanggal/
│   │   ├── AnnouncementCard.tsx
│   │   └── ShareButtons.tsx
│   └── decorative/
│       ├── ConfettiOverlay.tsx
│       ├── IslamicPattern.tsx
│       ├── MoonIcon.tsx
│       ├── LanternAnimation.tsx
│       └── CountdownTimer.tsx
├── lib/
│   ├── api.ts                  # API client (axios instance + interceptors)
│   ├── types.ts                # TypeScript interfaces
│   ├── constants.ts            # App constants (API URL, hari list, etc)
│   ├── utils.ts                # Helper functions (formatRupiah, etc)
│   └── validations.ts          # Zod schemas
├── hooks/
│   ├── useRegistrasi.ts
│   ├── useDashboard.ts
│   ├── useVoting.ts
│   ├── useTanggal.ts
│   └── useLocalStorage.ts
├── stores/
│   └── formStore.ts            # Zustand store for multi-step form state
├── public/
│   ├── images/
│   │   ├── hero-bg.webp
│   │   ├── islamic-pattern.svg
│   │   └── lantern.svg
│   └── fonts/
├── tailwind.config.ts
├── next.config.ts
└── .env.local
```

### 10.3 Contoh TypeScript Interfaces (`lib/types.ts`)

```typescript
// ─── API Response Types ──────────────────────────────────────
export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface PaginatedResponse<T> extends ApiResponse<T[]> {
  meta: {
    total: number;
    page: number;
    per_page: number;
    last_page: number;
  };
}

// ─── Domain Types ────────────────────────────────────────────
export type Hari = 'senin' | 'selasa' | 'rabu' | 'kamis' | 'jumat' | 'sabtu' | 'minggu';
export type Minggu = 1 | 2 | 3 | 4;

export interface Peserta {
  id: number;
  uuid: string;
  nama_lengkap: string;
  minggu: Minggu;
  hari: Hari[];
  budget_per_orang: number;
  catatan?: string;
  lokasi?: Lokasi;
  created_at: string;
}

export interface Lokasi {
  id: number;
  nama_tempat: string;
  alamat?: string;
  latitude?: number;
  longitude?: number;
  total_votes?: number;
  percentage?: number;
}

export interface DashboardStats {
  total_peserta: number;
  rata_rata_budget: number;
  min_budget: number;
  max_budget: number;
  minggu_terfavorit: {
    minggu: Minggu;
    jumlah_peserta: number;
  } | null;
  distribusi_minggu: { minggu: Minggu; jumlah: number }[];
}

export interface ChartData {
  hari?: string;
  label?: string;
  jumlah: number;
}

export interface VotingData {
  is_voting_open: boolean;
  deadline: string;
  lokasi: Lokasi[];
  total_voters: number;
}

export interface TanggalFinal {
  is_locked: boolean;
  tanggal: string;
  hari: string;
  jam: string;
  lokasi: Lokasi;
  estimasi_budget: number;
  total_peserta: number;
  catatan?: string;
}

// ─── Form Input Types ────────────────────────────────────────
export interface RegistrasiInput {
  nama_lengkap: string;
  minggu: Minggu;
  hari: Hari[];
  budget_per_orang: number;
  catatan?: string;
  lokasi: {
    nama_tempat: string;
    alamat?: string;
    latitude?: number;
    longitude?: number;
  };
}

export interface VoteInput {
  lokasi_id: number;
  voter_name: string;
}
```

### 10.4 Contoh API Client (`lib/api.ts`)

```typescript
import axios from 'axios';
import type {
  ApiResponse, PaginatedResponse, Peserta,
  DashboardStats, ChartData, VotingData,
  TanggalFinal, RegistrasiInput, VoteInput,
} from './types';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1',
  headers: { 'Content-Type': 'application/json' },
  timeout: 10000,
});

// ─── Registrasi ──────────────────────────────────────────────
export const submitRegistrasi = (data: RegistrasiInput) =>
  api.post<ApiResponse<Peserta>>('/registrasi', data);

export const getRegistrasi = (page = 1) =>
  api.get<PaginatedResponse<Peserta>>('/registrasi', { params: { page } });

export const getRegistrasiByUuid = (uuid: string) =>
  api.get<ApiResponse<Peserta>>(`/registrasi/${uuid}`);

// ─── Dashboard ───────────────────────────────────────────────
export const getDashboardStats = () =>
  api.get<ApiResponse<DashboardStats>>('/dashboard/stats');

export const getChartHari = () =>
  api.get<ApiResponse<ChartData[]>>('/dashboard/chart/hari');

export const getChartMinggu = () =>
  api.get<ApiResponse<ChartData[]>>('/dashboard/chart/minggu');

export const getChartBudget = () =>
  api.get<ApiResponse<{ ranges: ChartData[] }>>('/dashboard/chart/budget');

export const getResponden = (page = 1) =>
  api.get<PaginatedResponse<Peserta>>('/dashboard/responden', { params: { page } });

// ─── Voting ──────────────────────────────────────────────────
export const getVoting = () =>
  api.get<ApiResponse<VotingData>>('/voting');

export const submitVote = (data: VoteInput) =>
  api.post<ApiResponse<null>>('/voting', data);

export const getVotingHasil = () =>
  api.get<ApiResponse<VotingData>>('/voting/hasil');

// ─── Tanggal ─────────────────────────────────────────────────
export const getTanggalFinal = () =>
  api.get<ApiResponse<TanggalFinal>>('/tanggal');
```

### 10.5 Contoh Komponen: StatCard

```tsx
'use client';

import { motion } from 'framer-motion';
import { useEffect, useState } from 'react';
import { LucideIcon } from 'lucide-react';

interface StatCardProps {
  icon: LucideIcon;
  label: string;
  value: number;
  format?: 'number' | 'currency' | 'week';
  color?: string;
}

export default function StatCard({ icon: Icon, label, value, format = 'number', color = 'emerald' }: StatCardProps) {
  const [displayValue, setDisplayValue] = useState(0);

  useEffect(() => {
    // Animated counter
    const duration = 1500;
    const steps = 60;
    const increment = value / steps;
    let current = 0;
    const timer = setInterval(() => {
      current += increment;
      if (current >= value) {
        setDisplayValue(value);
        clearInterval(timer);
      } else {
        setDisplayValue(Math.floor(current));
      }
    }, duration / steps);
    return () => clearInterval(timer);
  }, [value]);

  const formatValue = (val: number) => {
    if (format === 'currency') return `Rp ${val.toLocaleString('id-ID')}`;
    if (format === 'week') return `Minggu ke-${val}`;
    return val.toLocaleString('id-ID');
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className={`bg-surface rounded-xl p-6 border border-white/5
        hover:border-${color}-500/30 transition-all duration-300
        hover:shadow-lg hover:shadow-${color}-500/10`}
    >
      <div className="flex items-center gap-3 mb-3">
        <div className={`p-2 rounded-lg bg-${color}-500/10`}>
          <Icon className={`w-5 h-5 text-${color}-400`} />
        </div>
        <span className="text-sm text-secondary">{label}</span>
      </div>
      <p className="text-3xl font-bold text-primary">
        {formatValue(displayValue)}
      </p>
    </motion.div>
  );
}
```

### 10.6 Performance Frontend

- **SSR/SSG**: Landing page & dashboard (SEO + fast initial load).
- **Client-side**: Map, charts, forms (interaktif, tidak perlu SEO).
- **Image optimization**: Next.js `<Image>` component, WebP format.
- **Code splitting**: Dynamic imports untuk map & chart libraries.
- **Prefetch**: Link prefetch untuk halaman utama.
- **Lazy load**: Chart & map components hanya load saat visible.

### 10.7 SEO

- Metadata unik per halaman (`generateMetadata`).
- Open Graph tags untuk share preview.
- `robots.txt` + `sitemap.xml`.
- Structured data (JSON-LD) untuk event.
- Canonical URLs.

### 10.8 Tailwind Config Kustom

```typescript
// tailwind.config.ts
import type { Config } from 'tailwindcss';

const config: Config = {
  content: [
    './app/**/*.{js,ts,jsx,tsx}',
    './components/**/*.{js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#1B4332',
          light: '#2D6A4F',
          accent: '#40916C',
        },
        secondary: {
          DEFAULT: '#D4A017',
          light: '#F2D06B',
        },
        background: '#0A1628',
        surface: {
          DEFAULT: '#132238',
          light: '#1C3450',
        },
        text: {
          primary: '#F1F5F9',
          secondary: '#94A3B8',
        },
      },
      fontFamily: {
        heading: ['Plus Jakarta Sans', 'sans-serif'],
        body: ['Inter', 'sans-serif'],
        arabic: ['Amiri', 'serif'],
      },
      borderRadius: {
        card: '12px',
        button: '8px',
        modal: '16px',
      },
      animation: {
        'float': 'float 6s ease-in-out infinite',
        'glow': 'glow 2s ease-in-out infinite alternate',
        'counter': 'counter 1.5s ease-out forwards',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-20px)' },
        },
        glow: {
          '0%': { boxShadow: '0 0 5px rgba(212, 160, 23, 0.2)' },
          '100%': { boxShadow: '0 0 20px rgba(212, 160, 23, 0.4)' },
        },
      },
    },
  },
  plugins: [],
};

export default config;
```

---

## 11) Cache & Strategi Performa

### 11.1 Apa yang Di-cache? (Backend)

| Data            | TTL       | Invalidasi                            |
| --------------- | --------- | ------------------------------------- |
| Dashboard stats | 60 detik  | Clear saat ada registrasi/delete baru |
| Chart hari      | 60 detik  | Clear saat ada registrasi baru        |
| Chart budget    | 60 detik  | Clear saat ada registrasi baru        |
| List lokasi     | 120 detik | Clear saat ada lokasi baru            |
| Voting results  | 30 detik  | Clear saat ada vote baru              |
| Tanggal final   | 300 detik | Clear saat admin update               |
| Event settings  | 600 detik | Clear saat admin update               |

### 11.2 Frontend Caching

- `stale-while-revalidate` pattern via SWR/TanStack Query.
- Static pages (landing, tanggal) di-cache di CDN.
- API responses dengan `Cache-Control` headers.

### 11.3 Database Query Optimization

- Gunakan `SELECT` spesifik (bukan `SELECT *`).
- Eager loading relationships (avoid N+1).
- Index pada kolom yang sering di-query (minggu, hari, lokasi_id).
- Aggregate queries (`COUNT`, `AVG`, `GROUP BY`) menggunakan raw queries jika perlu.

---

## 12) Security Checklist

### 12.1 Input Validation & Sanitization

- Semua input divalidasi di **Form Request** (server-side).
- Sanitasi HTML/script injection (`strip_tags`, `htmlspecialchars`).
- Prepared statements (Eloquent default) → aman dari SQL injection.
- File upload (jika ada): validasi MIME type, max size, rename file.

### 12.2 CSRF & CORS

- Laravel CSRF protection aktif untuk web routes.
- API routes: CORS dikonfigurasi di `config/cors.php`.
- Whitelist hanya domain frontend yang diizinkan.

### 12.3 Rate Limiting

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('registrasi', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

RateLimiter::for('voting', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip());
});

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->ip());
});
```

### 12.4 Anti-Duplikasi (Peserta & Voting)

- **Peserta**: cek `device_fingerprint` + `ip_address` + `nama_lengkap` combination.
- **Voting**: cek `session_token` + `voter_ip` (unique constraint di DB).
- **Fallback**: cookie-based check di frontend.

### 12.5 Admin Authentication

- Laravel Sanctum token-based auth.
- Password hashed dengan bcrypt (Laravel default).
- Token expiry: 24 jam.
- Logout: revoke semua tokens.

### 12.6 Security Headers (Nginx)

```nginx
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com" always;
```

---

## 13) Deployment & Hosting

### 13.1 Environment Setup

**Development**

```
Laravel: php artisan serve (localhost:8000)
Next.js: npm run dev (localhost:3000)
MySQL: localhost:3306
```

**Production (VPS)**

```
Nginx → reverse proxy
  ├── / → Next.js (PM2, port 3000)
  └── /api/ → Laravel (PHP-FPM, port 9000 via Nginx fastcgi)
MySQL: localhost:3306 (atau managed DB)
SSL: Let's Encrypt (certbot)
```

### 13.2 Nginx Config (Production)

```nginx
server {
    listen 80;
    server_name bukber.example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name bukber.example.com;

    ssl_certificate /etc/letsencrypt/live/bukber.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bukber.example.com/privkey.pem;

    # Next.js frontend
    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }

    # Laravel API
    location /api {
        alias /var/www/bukber-api/public;
        try_files $uri $uri/ @laravel;

        location ~ \.php$ {
            fastcgi_pass unix:/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }

    @laravel {
        rewrite ^/api/(.*)$ /api/index.php?$query_string last;
    }

    # Security headers
    include /etc/nginx/snippets/security-headers.conf;

    # Gzip
    gzip on;
    gzip_types text/plain application/json application/javascript text/css;
    gzip_min_length 1000;
}
```

### 13.3 Environment Variables

**Laravel (`.env`)**

```env
APP_NAME="Bukber Ramadhan"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://bukber.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bukber_db
DB_USERNAME=bukber_user
DB_PASSWORD=secret_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=bukber@example.com
MAIL_FROM_NAME="Bukber Ramadhan"

SANCTUM_STATEFUL_DOMAINS=bukber.example.com
```

**Next.js (`.env.local`)**

```env
NEXT_PUBLIC_API_URL=https://bukber.example.com/api/v1
NEXT_PUBLIC_APP_NAME=Bukber Ramadhan 2026
NEXT_PUBLIC_GOOGLE_MAPS_KEY=AIza...  # Jika pakai Google Maps
```

### 13.4 Deploy Checklist

```
[ ] Domain + SSL aktif, redirect HTTP → HTTPS
[ ] MySQL database created + user privileges set
[ ] Laravel: composer install --optimize-autoloader --no-dev
[ ] Laravel: php artisan migrate --force
[ ] Laravel: php artisan db:seed (AdminSeeder, EventSettingSeeder)
[ ] Laravel: php artisan config:cache
[ ] Laravel: php artisan route:cache
[ ] Laravel: php artisan view:cache
[ ] Next.js: npm run build
[ ] Next.js: pm2 start npm --name "bukber-web" -- start
[ ] Nginx config tested (nginx -t) dan reloaded
[ ] PHP-FPM running
[ ] Redis running (jika dipakai)
[ ] Cron job untuk Laravel scheduler: * * * * * php artisan schedule:run
[ ] Queue worker running: php artisan queue:work --daemon
[ ] Backup MySQL scheduled (daily)
[ ] Monitoring basic (uptime check)
```

### 13.5 Backup & Recovery

- **MySQL**: `mysqldump` harian via cron, simpan di storage terpisah.
- **Retention**: 7 hari rolling, 1 backup mingguan (30 hari).
- **Recovery test**: minimal 1x sebelum go-live.

---

## 14) Testing & QA

### 14.1 Backend Testing (Laravel)

**Unit Tests**

```php
// tests/Unit/DashboardServiceTest.php
public function test_get_stats_returns_correct_structure(): void
{
    Peserta::factory()->count(10)->create();

    $stats = app(DashboardService::class)->getStats();

    $this->assertArrayHasKey('total_peserta', $stats);
    $this->assertArrayHasKey('rata_rata_budget', $stats);
    $this->assertArrayHasKey('minggu_terfavorit', $stats);
    $this->assertEquals(10, $stats['total_peserta']);
}
```

**Feature Tests**

```php
// tests/Feature/RegistrasiTest.php
public function test_can_submit_registrasi(): void
{
    $response = $this->postJson('/api/v1/registrasi', [
        'nama_lengkap' => 'Ahmad Fauzi',
        'minggu' => 2,
        'hari' => ['senin', 'rabu', 'jumat'],
        'budget_per_orang' => 50000,
        'lokasi' => [
            'nama_tempat' => 'Warung Sate',
            'alamat' => 'Jl. Merdeka No. 10',
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success', 'message',
            'data' => ['id', 'uuid', 'nama_lengkap', 'minggu'],
        ]);

    $this->assertDatabaseHas('peserta', [
        'nama_lengkap' => 'Ahmad Fauzi',
        'minggu' => 2,
    ]);
}

public function test_registrasi_validation_fails_without_nama(): void
{
    $response = $this->postJson('/api/v1/registrasi', [
        'minggu' => 2,
        'hari' => ['senin'],
        'budget_per_orang' => 50000,
        'lokasi' => ['nama_tempat' => 'Test'],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['nama_lengkap']);
}
```

**Commands**

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=RegistrasiTest

# Run with coverage
php artisan test --coverage --min=80
```

### 14.2 Frontend Testing

**Component Test (example)**

```typescript
// __tests__/StatCard.test.tsx
import { render, screen } from '@testing-library/react';
import StatCard from '@/components/dashboard/StatCard';
import { Users } from 'lucide-react';

describe('StatCard', () => {
  it('renders label and value', () => {
    render(<StatCard icon={Users} label="Total Peserta" value={25} />);
    expect(screen.getByText('Total Peserta')).toBeInTheDocument();
  });
});
```

**E2E Test (Playwright)**

```typescript
// e2e/registrasi.spec.ts
import { test, expect } from '@playwright/test';

test('user can complete registration flow', async ({ page }) => {
  await page.goto('/daftar');

  // Step 1: Fill nama
  await page.fill('[name="nama_lengkap"]', 'Ahmad Fauzi');

  // Step 2: Select minggu
  await page.click('[data-minggu="2"]');

  // Step 3: Select hari
  await page.click('[data-hari="senin"]');
  await page.click('[data-hari="rabu"]');

  // Step 4: Set budget
  await page.fill('[name="budget_per_orang"]', '50000');

  // Submit & navigate to lokasi
  await page.click('button[type="submit"]');
  await expect(page).toHaveURL(/\/lokasi/);

  // Select location
  await page.fill('[name="nama_tempat"]', 'Warung Sate');
  await page.click('#confirm-lokasi');

  // Should reach selesai page
  await expect(page).toHaveURL(/\/selesai/);
  await expect(page.locator('.konfirmasi-card')).toBeVisible();
});
```

---

## 15) Observability & Monitoring

### Minimal (MVP)

- Laravel log files (`storage/logs/`).
- Health check endpoint: `GET /api/health` → `{ "status": "ok" }`.
- Uptime monitoring (UptimeRobot / BetterStack — free tier).
- Error tracking: Laravel log + email notification untuk 500 errors.

### Menengah

- Laravel Telescope (development/staging).
- Sentry / Bugsnag untuk error tracking (production).
- Basic metrics: jumlah registrasi/hari, response time, error rate.
- MySQL slow query log enabled.
- Disk space monitoring.

---

## 16) Edge Cases yang Wajib Di-handle

| Case                                                    | Handling                                                      |
| ------------------------------------------------------- | ------------------------------------------------------------- |
| Deadline registrasi lewat                               | Tampilkan pesan "Pendaftaran sudah ditutup" + disable form    |
| Deadline voting lewat                                   | Tampilkan hasil final, disable tombol vote                    |
| Tanggal belum di-lock                                   | Tampilkan "Tanggal belum ditentukan" + countdown/CTA          |
| Nama peserta duplikat                                   | Warning "Nama ini sudah terdaftar, apakah Anda yakin?"        |
| Budget di luar range                                    | Validasi real-time + pesan error inline                       |
| Map tidak load (no internet / API error)                | Fallback ke text-only input lokasi                            |
| 0 peserta terdaftar                                     | Dashboard empty state: ilustrasi + CTA "Jadi yang pertama!"   |
| Vote tanpa nama                                         | Validasi required + error message                             |
| Admin lupa password                                     | Reset via CLI (`php artisan admin:reset-password`)          |
| Mobile user                                             | Responsive layout, bottom sheet untuk filter, swipeable cards |
| Slow connection                                         | Skeleton loaders, optimized images, minimal JS bundle         |
| IP-based rate limit false positive (kantor/wifi publik) | Kombinasi IP + session token, bukan IP saja                   |

---

## 17) Go-Live Checklist

```
[ ] Domain + SSL aktif & redirect HTTP→HTTPS berfungsi
[ ] Database production sudah di-migrate + seeder admin
[ ] Rate limit aktif untuk registrasi (5/menit) & voting (10/menit)
[ ] CORS hanya whitelist domain frontend
[ ] Index database sudah dibuat
[ ] Caching berfungsi (dashboard stats, voting results)
[ ] Error pages (404, 500, maintenance) terintegrasi
[ ] Admin bisa login & manage peserta
[ ] Deadline registrasi & voting bisa di-set dari admin
[ ] Form registrasi → lokasi → selesai flow berjalan end-to-end
[ ] Dashboard menampilkan data real dari database
[ ] Voting berfungsi + anti-duplikasi aktif
[ ] Penentuan tanggal bisa di-lock dan ditampilkan
[ ] Share ke WhatsApp berfungsi
[ ] Backup MySQL terjadwal + test restore 1x
[ ] Monitoring uptime aktif
[ ] Mobile responsive ditest di 3+ device/resolusi
[ ] Performance: Lighthouse score > 80 (Performance, Accessibility, SEO)
[ ] Security headers terpasang di Nginx
[ ] .env production TIDAK ter-commit ke git
```

---

## 18) Lampiran A — Contoh Migration Laravel

```php
<?php
// database/migrations/2026_01_01_000001_create_peserta_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('nama_lengkap', 100);
            $table->tinyInteger('minggu')->comment('Minggu Ramadhan 1-4');
            $table->unsignedInteger('budget_per_orang')->comment('Budget dalam Rupiah');
            $table->text('catatan')->nullable();
            $table->string('device_fingerprint', 64)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('minggu');
            $table->index('created_at');
        });

        Schema::create('peserta_hari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->enum('hari', ['senin','selasa','rabu','kamis','jumat','sabtu','minggu']);

            $table->index('hari');
            $table->index('peserta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_hari');
        Schema::dropIfExists('peserta');
    }
};
```

---

## 19) Lampiran B — Contoh Zod Validation Schema (Frontend)

```typescript
// lib/validations.ts
import { z } from 'zod';

const hariEnum = z.enum([
  'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'
]);

export const registrasiSchema = z.object({
  nama_lengkap: z
    .string()
    .min(3, 'Nama minimal 3 karakter')
    .max(100, 'Nama maksimal 100 karakter')
    .regex(/^[a-zA-Z\s'.]+$/, 'Nama hanya boleh huruf dan spasi'),

  minggu: z
    .number()
    .int()
    .min(1, 'Pilih minggu Ramadhan')
    .max(4, 'Minggu maksimal 4'),

  hari: z
    .array(hariEnum)
    .min(1, 'Pilih minimal 1 hari yang bisa'),

  budget_per_orang: z
    .number()
    .int()
    .min(10000, 'Budget minimal Rp 10.000')
    .max(1000000, 'Budget maksimal Rp 1.000.000'),

  catatan: z
    .string()
    .max(500, 'Catatan maksimal 500 karakter')
    .optional(),

  lokasi: z.object({
    nama_tempat: z
      .string()
      .min(3, 'Nama tempat minimal 3 karakter')
      .max(200),
    alamat: z.string().max(500).optional(),
    latitude: z.number().min(-90).max(90).optional(),
    longitude: z.number().min(-180).max(180).optional(),
  }),
});

export type RegistrasiFormData = z.infer<typeof registrasiSchema>;

export const voteSchema = z.object({
  lokasi_id: z.number().int().positive('Pilih lokasi untuk di-vote'),
  voter_name: z
    .string()
    .min(3, 'Nama voter minimal 3 karakter')
    .max(100),
});
```

---

## 20) Lampiran C — Utility Functions (Frontend)

```typescript
// lib/utils.ts

/**
 * Format angka ke format Rupiah
 */
export function formatRupiah(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
}

/**
 * Format tanggal ke bahasa Indonesia
 */
export function formatTanggal(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

/**
 * Capitalize nama hari
 */
export function capitalizeHari(hari: string): string {
  return hari.charAt(0).toUpperCase() + hari.slice(1);
}

/**
 * Generate WhatsApp share URL
 */
export function generateWhatsAppUrl(text: string): string {
  return `https://wa.me/?text=${encodeURIComponent(text)}`;
}

/**
 * Format countdown timer
 */
export function getCountdown(targetDate: string): {
  days: number; hours: number; minutes: number; seconds: number;
} {
  const diff = new Date(targetDate).getTime() - Date.now();
  if (diff <= 0) return { days: 0, hours: 0, minutes: 0, seconds: 0 };

  return {
    days: Math.floor(diff / (1000 * 60 * 60 * 24)),
    hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
    minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
    seconds: Math.floor((diff % (1000 * 60)) / 1000),
  };
}

/**
 * Warna untuk chart berdasarkan hari
 */
export const HARI_COLORS: Record<string, string> = {
  senin: '#3B82F6',
  selasa: '#8B5CF6',
  rabu: '#EC4899',
  kamis: '#F59E0B',
  jumat: '#10B981',
  sabtu: '#06B6D4',
  minggu: '#EF4444',
};
```

---

## 21) Catatan Penutup

Dokumen ini adalah **guidebook lengkap** untuk membangun website "Buka Bersama" Ramadhan dari nol hingga production-ready. Dengan mengikuti panduan ini, kamu akan memiliki:

1. ✅ **Backend Laravel** yang solid dengan REST API, validasi ketat, caching, dan security best practices.
2. ✅ **Frontend Next.js** yang modern dengan Tailwind CSS, animasi, peta interaktif, dan chart visual.
3. ✅ **Database MySQL** yang terstruktur dengan relasi, index, dan query optimization.
4. ✅ **User flow** yang jelas dari registrasi → lokasi → dashboard → voting → penentuan tanggal.
5. ✅ **Security & deployment** siap production dengan SSL, rate limiting, backup, dan monitoring.

> **Bismillah**, semoga projectnya lancar dan bukber-nya seru! 🌙✨

---

*Dokumen ini dibuat sebagai panduan developer untuk project Bukber Ramadhan. Versi: 1.0 | Terakhir diperbarui: Februari 2026*
