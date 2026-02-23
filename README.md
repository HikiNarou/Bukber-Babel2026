# BukberYuk - Bukber Babel 2026

Website platform untuk mengatur acara Buka Bersama (Bukber) menggunakan Laravel 11 dengan MySQL.

## Fitur
- Form Pendaftaran Bukber (Public)
- Dashboard Summary dengan statistik real-time
- Autentikasi (Login/Register)
- Bar Chart ketersediaan hari
- Heatmap ketersediaan jam

## Tech Stack
- Laravel 11 (PHP)
- MySQL Database
- Tailwind CSS (CDN)
- Blade Templating
- Google Material Symbols Icons

## Setup Lokal
1. Clone repository
2. `composer install`
3. Copy `.env.example` ke `.env` lalu isi konfigurasi database
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `php artisan serve`

## Deploy Laravel ke Render + Aiven MySQL + Vercel (Proxy Domain)

> Rekomendasi arsitektur untuk project ini:
> - **Render**: menjalankan Laravel (PHP app, route Blade, API, queue/web request)
> - **Aiven MySQL**: database production
> - **Vercel**: domain publik / edge proxy ke service Render (opsional, tapi cocok kalau mau URL dari Vercel)

### 1) Persiapan Aiven MySQL
1. Buat service **MySQL** di Aiven.
2. Di halaman service Aiven, catat:
   - `Host`
   - `Port`
   - `Database name`
   - `Username`
   - `Password`
   - **CA Certificate** (`ca.pem`)
3. Atur network access:
   - Rekomendasi: allowlist hanya IP yang benar-benar dibutuhkan (IP team/dev dan egress yang digunakan Render).
   - Hindari `0.0.0.0/0`. Kalau terpaksa untuk troubleshooting, batasi waktunya sesingkat mungkin lalu kunci lagi.
   - Untuk production, prioritaskan private networking (mis. VPC peering/PrivateLink jika plan Aiven + platform mendukung).
4. Wajib SSL/TLS:
   - Simpan cert Aiven sebagai file `ca.pem` (digunakan Laravel via `MYSQL_ATTR_SSL_CA`).

### 2) Deploy Laravel ke Render (Web Service)
1. Push repo ini ke GitHub.
2. Render Dashboard → **New** → **Web Service** → pilih repo ini.
3. Pilih:
   - **Environment**: `PHP`
   - **Region**: yang paling dekat user/Aiven region
4. Isi command Render:
   - **Opsi cepat (testing / non-production, native runtime):**
   - **Build Command**  
     `composer install --no-interaction --prefer-dist --optimize-autoloader && php artisan config:cache && php artisan route:cache && php artisan view:cache`
   - **Start Command**  
     `php artisan serve --host=0.0.0.0 --port=$PORT`
   - **Opsi production (direkomendasikan):** pakai **Dockerfile** (Apache/Nginx + PHP-FPM) dan buat Render sebagai Docker Web Service.
5. Tambahkan Environment Variables di Render:

   | Key | Value |
   |---|---|
   | `APP_NAME` | `BukberYuk` |
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_URL` | `https://<your-render-service-name>.onrender.com` |
   | `APP_KEY` | Generate lokal: `php artisan key:generate --show` |
   | `DB_CONNECTION` | `mysql` |
   | `DB_HOST` | dari Aiven |
   | `DB_PORT` | dari Aiven (umumnya `3306`) |
   | `DB_DATABASE` | dari Aiven |
   | `DB_USERNAME` | dari Aiven |
   | `DB_PASSWORD` | dari Aiven |
   | `MYSQL_ATTR_SSL_CA` | path file CA di container (lihat langkah CA di bawah) |
   | `SESSION_DRIVER` | `database` |

6. Upload CA cert Aiven:
   - Karena Laravel baca `MYSQL_ATTR_SSL_CA` sebagai path file, simpan `ca.pem` di image/deploy path (contoh `/etc/ssl/certs/aiven-ca.pem`).
   - Set `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/aiven-ca.pem`.
   - Jika tetap pakai native runtime, langkah explicit:
     1. Render Dashboard → Environment/Secrets → tambah secret file berisi isi `ca.pem`.
     2. Mount secret file ke path tetap, contoh `/etc/ssl/certs/aiven-ca.pem`.
     3. Set env `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/aiven-ca.pem`.
   - Untuk production, paling aman build via Docker supaya cert pasti ada di path tersebut.
   - Contoh Dockerfile minimal (Apache + cert):

```dockerfile
FROM php:8.3-apache
RUN apt-get update && apt-get install -y git unzip libzip-dev && docker-php-ext-install pdo_mysql
WORKDIR /var/www/html
COPY . .
COPY ca.pem /etc/ssl/certs/aiven-ca.pem
RUN a2enmod rewrite \
    && cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.bak \
    && sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --prefer-dist --optimize-autoloader \
    && php artisan config:cache
CMD apache2-foreground
```

### 3) Verifikasi Render
1. Buka shell Render lalu test:
   - Jalankan migrasi sekali tiap release: `php artisan migrate --force`
   - `php artisan migrate:status`
   - `php artisan tinker` lalu query sederhana model.
2. Hit endpoint aplikasi:
   - `GET /` harus `200`
   - Coba submit form registrasi dan cek data masuk ke Aiven.
3. Cek log Render jika ada error:
   - koneksi DB (`SQLSTATE[HY000] [2002]`)
   - key app belum terisi
   - mismatch SSL CA path

### 4) Deploy Vercel sebagai Proxy ke Render
Karena project ini full Laravel (server-side Blade), jalankan app utama di Render.  
Vercel digunakan sebagai entry domain + edge proxy.

1. Import repo ke Vercel.
2. Di `Project Settings`:
   - Framework preset: **Other**
   - Build Command: kosongkan
   - Output Directory: kosongkan
3. Tambahkan `vercel.json` berikut di root project (ubah destination ke URL Render kamu):

```json
{
  "rewrites": [
    {
      "source": "/(.*)",
      "destination": "https://<your-render-service-name>.onrender.com/$1"
    }
  ]
}
```

4. Deploy. Setelah itu semua request ke domain Vercel diteruskan ke Laravel di Render.

### 5) Konfigurasi Domain & Keamanan
1. Pakai domain custom di Vercel, lalu arahkan `APP_URL` di Render ke domain final.
2. Kalau pakai cookie/session auth:
   - set domain cookie sesuai domain final
   - pastikan HTTPS aktif end-to-end
3. Hardening:
   - `APP_DEBUG=false`
   - rotate password DB Aiven berkala
   - batasi IP akses Aiven (jangan biarkan `0.0.0.0/0` permanen)
   - backup otomatis Aiven aktif

### 6) Checklist Go-Live
- [ ] `APP_KEY` terisi
- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Render bisa konek ke Aiven via TLS
- [ ] Migrasi release sudah dijalankan (`php artisan migrate --force`)
- [ ] Form registrasi bisa insert data
- [ ] Dashboard bisa baca data
- [ ] Domain Vercel me-rewrite ke Render dengan status 200

## License
MIT
