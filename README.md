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

## Setup
1. Clone repository
2. `composer install`
3. Copy `.env.example` to `.env` dan konfigurasi database
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `php artisan serve`

## License
MIT