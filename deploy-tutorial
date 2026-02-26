# Panduan Deploy Production ke DirectAdmin (sikma.live)

Dokumen ini sudah disesuaikan dengan kondisi code terbaru project ini:
- Backend: Laravel 11 (PHP >= 8.2) (`bukber-api`)
- Frontend: Next.js 16 App Router (`bukber-web`)
- API terbaru: multi-event (`/api/v1/events/...`)
- Hosting target: Shared Hosting DirectAdmin
- Domain: `sikma.live` (frontend), `api.sikma.live` (backend)

## Update Penting (Feb 2026)

Code frontend terbaru memakai dynamic routes:
- `/bukber/[eventSlug]/*`
- `/admin/events/[eventId]`

Karena itu, mode `output: export` akan gagal tanpa `generateStaticParams()` untuk semua route dinamis.
Error yang muncul biasanya:
- `missing generateStaticParams()`
- atau error route handler `/api/bff/[...path]` saat export

Kesimpulan praktis:
- Deploy utama yang direkomendasikan sekarang: frontend jalan sebagai Node.js app (SSR), bukan static export.
- Mode static export hanya untuk skenario khusus setelah refactor route dinamis.

## 1) Arsitektur Deploy yang Direkomendasikan

Untuk code saat ini:
- `https://sikma.live` -> frontend Next.js (Node.js app / SSR)
- `https://api.sikma.live` -> Laravel API

Kenapa:
- Dynamic route tetap jalan tanpa rebuild tiap event baru.
- Tidak perlu workaround `generateStaticParams`.

## 2) Catatan Penting Versi Frontend Saat Ini (Wajib Baca)

Frontend sekarang punya route handler BFF (`/api/bff`) dengan `runtime = nodejs`.
BFF akan berjalan normal hanya jika frontend dijalankan di Node.js runtime.

Untuk mode deploy Node (recommended):
- `NEXT_PUBLIC_USE_BFF_PROXY=true`
- `BACKEND_API_URL` wajib diarahkan ke `https://api.sikma.live/api/v1`

Untuk mode static export (legacy/refactor only):
- `NEXT_PUBLIC_USE_BFF_PROXY=false`

## 3) Struktur Folder yang Benar di DirectAdmin

Prinsip:
- Source Laravel disimpan di luar web root.
- Web root API hanya berisi file publik Laravel (`public`).
- Frontend dijalankan sebagai Node.js app terpisah (bukan copy file `out` ke `public_html`).

Contoh struktur final:
- `APP_DIR=/home/sikmaliv/sikmaliv/apps/bukber-api` (sesuaikan hasil cek server)
- `API_WEBROOT=$HOME/domains/api.sikma.live/public_html`
- `MAIN_WEBROOT=$HOME/domains/sikma.live/public_html`

## 4) Preflight: Cek Path Real Server (Wajib)

Jalankan di terminal server:

```bash
pwd
ls -la ~
find "$HOME" -type d -name "bukber-api" 2>/dev/null
```

Set variabel kerja berdasarkan hasil real:

```bash
APP_DIR="/home/sikmaliv/sikmaliv/apps/bukber-api"
API_WEBROOT="$HOME/domains/api.sikma.live/public_html"
MAIN_WEBROOT="$HOME/domains/sikma.live/public_html"
```

Validasi cepat:

```bash
ls -l "$APP_DIR/artisan" "$APP_DIR/bootstrap/app.php"
ls -ld "$API_WEBROOT" "$MAIN_WEBROOT"
```

## 5) Konfigurasi DNS dan SSL

Pastikan:
- `A @` -> IP server
- `A api` -> IP server
- `CNAME www` -> `sikma.live`

Di DirectAdmin:
- Aktifkan Let's Encrypt untuk `sikma.live`, `www.sikma.live`, `api.sikma.live`
- Aktifkan force HTTPS
- Set PHP version untuk `api.sikma.live` ke 8.2 atau lebih baru

## 6) Deploy Backend Laravel (api.sikma.live)

## 6.1 Install dependency

```bash
cd "$APP_DIR"
composer install --no-dev --optimize-autoloader
```

Kalau composer tidak tersedia di server:
- Jalankan install di lokal
- Upload folder `vendor` ke server

## 6.2 Setup `.env` production

```bash
cd "$APP_DIR"
cp .env.production.example .env
```

Minimal yang harus benar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.sikma.live
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=DB_NAME
DB_USERNAME=DB_USER
DB_PASSWORD=DB_PASSWORD
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

CORS_ALLOWED_ORIGINS=https://sikma.live,https://www.sikma.live
SANCTUM_STATEFUL_API=false
SESSION_DOMAIN=.sikma.live
SESSION_SECURE_COOKIE=true

ADMIN_DEFAULT_USERNAME=admin
ADMIN_DEFAULT_EMAIL=admin@sikma.live
ADMIN_DEFAULT_PASSWORD=GANTI_DENGAN_PASSWORD_ACAK_KUAT
```

Generate app key:

```bash
php artisan key:generate --force
```

## 6.3 Migrasi dan seeding

```bash
cd "$APP_DIR"
php artisan migrate --force
php artisan db:seed --force
```

## 6.4 Permission

```bash
cd "$APP_DIR"
chmod -R 775 storage bootstrap/cache
chmod 640 .env
php artisan storage:link
```

## 6.5 Publish `public/` Laravel ke API web root

Kosongkan webroot API lalu copy isi `public`:

```bash
find "$API_WEBROOT" -mindepth 1 ! -name 'cgi_bin' -exec rm -rf {} +
cp -av "$APP_DIR/public/." "$API_WEBROOT/"
```

## 6.6 Pastikan `index.php` API benar (full replacement)

Ini penting untuk menghindari error 500 karena file campur.

```bash
cat > "$API_WEBROOT/index.php" <<PHP
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(\$maintenance = '$APP_DIR/storage/framework/maintenance.php')) {
    require \$maintenance;
}

require '$APP_DIR/vendor/autoload.php';

\$app = require_once '$APP_DIR/bootstrap/app.php';

\$app->handleRequest(Request::capture());
PHP
```

Syntax check:

```bash
php -l "$API_WEBROOT/index.php"
```

## 6.7 Optimasi cache Laravel

```bash
cd "$APP_DIR"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6.8 Verifikasi API

Wajib cek:
- `https://api.sikma.live/api/health`
- `https://api.sikma.live/api/v1/events`

Catatan:
- Endpoint `/api/health` sekarang juga menampilkan readiness database (`ok` / `degraded`).
- Jika `degraded`, cek migrasi dan konfigurasi DB.

## 7) Deploy Frontend Next.js (Node.js Runtime - Recommended)

Karena app memakai route dinamis, frontend sebaiknya dijalankan sebagai Node.js app.

## 7.1 Siapkan environment frontend production

Di server, pada folder frontend (`WEB_APP_DIR`), buat `.env.production`:

```env
NEXT_PUBLIC_API_URL=https://api.sikma.live/api/v1
NEXT_PUBLIC_API_URL_FALLBACKS=
NEXT_PUBLIC_APP_NAME=Bukber Ramadhan 2026
BACKEND_API_URL=https://api.sikma.live/api/v1
BACKEND_API_URL_FALLBACKS=
NEXT_PUBLIC_USE_BFF_PROXY=true
```

Penting:
- Jangan set `NEXT_OUTPUT_MODE=export`
- Jika sebelumnya pernah set env itu, hapus dulu di shell session

## 7.2 Build frontend (tanpa export mode)

```bash
cd "WEB_APP_DIR"
npm ci
npm run build
```

Jika build sukses, lanjut start via Node.js App Manager di DirectAdmin.

## 7.3 Jalankan app Next.js di DirectAdmin Node.js App Manager

Isi form Create Application dengan format berikut (sesuaikan path real):

- `Node.js version` -> **20.x atau 22.x** (minimum Node 20 untuk Next.js 16)
- `Application mode` -> `Production`
- `Application root` -> `sikmaliv/apps/bukber-web` (relative dari home)
- `Application URL` -> domain `sikma.live`, path kosong `/`
- `Application startup file` -> `server.js`
- `Passenger log file` -> `/home/sikmaliv/logs/bukber-web-passenger.log`

Jika dropdown hanya menampilkan Node `10.24.1` dan tidak ada opsi 20+:
- Frontend Next.js 16 **tidak bisa** dijalankan di hosting ini.
- Solusi: minta host aktifkan Node 20/22, atau deploy frontend di platform lain (Vercel/Cloudflare) dan tetap pakai API `api.sikma.live`.

Setelah klik `CREATE`, upload source frontend ke folder app root tersebut, lalu jalankan:

```bash
WEB_APP_DIR="$HOME/sikmaliv/apps/bukber-web"
cd "$WEB_APP_DIR"
ls -l server.js
npm ci
npm run build
```

Catatan:
- File `server.js` sekarang sudah disediakan di repository `bukber-web/server.js`.
- Jika `ls -l server.js` menunjukkan file tidak ada, buat manual dengan isi berikut:

```js
const http = require("http");
const next = require("next");

const host = process.env.HOST || "0.0.0.0";
const port = Number.parseInt(process.env.PORT || "3000", 10);
const dev = process.env.NODE_ENV !== "production";
const app = next({ dev, dir: __dirname, hostname: host, port });
const handle = app.getRequestHandler();

app
  .prepare()
  .then(() => {
    http.createServer((req, res) => handle(req, res)).listen(port, host);
  })
  .catch((error) => {
    console.error("Failed to start Next.js server:", error);
    process.exit(1);
  });
```

Lalu di halaman Node.js App Manager:
- Klik `Restart`
- Pastikan status app `Running`
- Buka `https://sikma.live`

## 7.4 Mapping domain utama

Pastikan `sikma.live` diarahkan ke Node.js app frontend.

Jika panel kamu tetap memakai `public_html` sebagai docroot, gunakan reverse proxy dari domain ke Node app sesuai fitur App Manager host kamu.

## 7.5 Legacy mode static export (tidak direkomendasikan untuk code saat ini)

Static export hanya layak jika kamu refactor semua route dinamis agar tidak butuh `generateStaticParams()`.

## 8) Checklist Final Go-Live

- [ ] `https://sikma.live` menampilkan frontend Bukber, bukan halaman default DirectAdmin
- [ ] `https://api.sikma.live/api/health` status `ok` atau minimal bukan error 500
- [ ] `https://api.sikma.live/api/v1/events` mengembalikan data JSON
- [ ] Halaman `/bukber` di frontend bisa load daftar event
- [ ] Registrasi event berhasil
- [ ] Voting berhasil
- [ ] Login admin berhasil
- [ ] Tidak ada error CORS di browser
- [ ] `APP_DEBUG=false`
- [ ] SSL aktif valid di semua domain

## 9) Cron Job (Opsional, Recommended)

Scheduler:

```cron
* * * * * /usr/local/bin/php /home/sikmaliv/sikmaliv/apps/bukber-api/artisan schedule:run >> /dev/null 2>&1
```

Queue worker periodik:

```cron
*/5 * * * * /usr/local/bin/php /home/sikmaliv/sikmaliv/apps/bukber-api/artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

Sesuaikan path jika `APP_DIR` kamu berbeda.

## 10) Troubleshooting Cepat

## Kasus A: API error 500

Cek:

```bash
tail -n 120 "$APP_DIR/storage/logs/laravel.log"
tail -n 120 "$HOME/domains/api.sikma.live/logs/error.log"
```

Penyebab umum:
- `index.php` API masih campur/keliru
- Path absolut ke `vendor/autoload.php` salah
- Permission `storage` / `bootstrap/cache` salah
- PHP version domain API < 8.2

## Kasus B: Frontend masih halaman default DirectAdmin

Penyebab:
- Domain `sikma.live` masih diarahkan ke docroot default, bukan ke Node.js app

Solusi:
- Cek mapping domain di Node.js App Manager
- Restart app Node.js
- Pastikan app status `running`

## Kasus C: Frontend blank atau request API gagal

Cek:
- `.env.production` frontend saat build
- `NEXT_PUBLIC_USE_BFF_PROXY=true` untuk mode Node runtime
- `BACKEND_API_URL` harus `https://api.sikma.live/api/v1`
- Rebuild frontend setelah ubah env (`npm run build`) lalu restart app

## Kasus E: `next build` gagal dengan `Resource temporarily unavailable`

Ini biasanya limit thread/process CloudLinux saat Turbopack build.

Gunakan fallback build webpack:

```bash
cd "$HOME/sikmaliv/apps/bukber-web"
export NODE_ENV=development
export NPM_CONFIG_PRODUCTION=false
npm ci --include=dev
npm run build:webpack
```

Jika berhasil, restart app di Node.js App Manager.

## Kasus F: Build server tetap gagal (`EAGAIN`) walau sudah `--webpack`

Ini berarti limit process/thread akun hosting terlalu ketat (CloudLinux).
Solusi aman: build di lokal, lalu upload hasil build ke server.

### F.1 Build di lokal (Windows)

```powershell
cd "E:\EngganNgoding\NGETES ILMU\YukBukber Cosmic\bukber-web"
npm ci
npm run build
```

Pastikan folder `.next` sudah terbentuk.

### F.2 Upload ke server (`WEB_APP_DIR`)

Upload ke `/home/sikmaliv/sikmaliv/apps/bukber-web`:
- `.next`
- `app`
- `components`
- `hooks`
- `lib`
- `public`
- `package.json`
- `package-lock.json`
- `next.config.ts`
- `postcss.config.mjs`
- `tsconfig.json`
- `server.js`
- `.env.production`

Jangan upload:
- `node_modules`

### F.3 Install dependency di server tanpa build

```bash
source /home/sikmaliv/nodevenv/sikmaliv/apps/bukber-web/20/bin/activate
cd /home/sikmaliv/sikmaliv/apps/bukber-web
test -f server.js || { echo "server.js missing"; exit 1; }
export NODE_ENV=development
export NPM_CONFIG_PRODUCTION=false
npm ci --include=dev
```

### F.4 Restart app

- Klik `RESTART` di Node.js App Manager
- Test `https://sikma.live`

### F.5 Jika muncul `Cannot find module .../server.js`

Artinya startup file Node.js app (`server.js`) tidak ada di folder app root.

Perbaikan cepat:

```bash
WEB_APP_DIR="/home/sikmaliv/sikmaliv/apps/bukber-web"
cd "$WEB_APP_DIR"
ls -l server.js
```

- Jika file belum ada: upload ulang file `bukber-web/server.js` dari repo ini ke `WEB_APP_DIR`.
- Setelah file ada: jalankan lagi `npm ci --include=dev`, lalu `RESTART` app di Node.js App Manager.

## Kasus D: Salah upload frontend ke API domain

Jika `api.sikma.live/public_html` berisi folder `_next`, `dashboard`, `voting`, dll:
- Itu salah lokasi upload
- Hapus isi API webroot
- Copy ulang Laravel `public` ke API webroot

## 11) SOP Update Deploy Berikutnya

1. Backup database dulu.
2. Deploy API:
   - Upload update source ke `APP_DIR`
   - `composer install --no-dev --optimize-autoloader`
   - `php artisan migrate --force`
   - `php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache`
3. Deploy frontend:
   - Build ulang normal: `npm ci && npm run build` (tanpa `NEXT_OUTPUT_MODE=export`)
   - Restart app Node.js di DirectAdmin
4. Smoke test endpoint utama:
   - `/api/health`
   - `/api/v1/events`
   - `/bukber`
