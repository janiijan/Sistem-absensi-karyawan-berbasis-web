# Sistem Absensi Karyawan Berbasis Web

Aplikasi absensi karyawan (Laravel + MySQL) dengan UI sesuai SRS dan Figma **Sepa Indonesia**, dijalankan via Docker.

## Stack

- Laravel 12 (PHP 8.3)
- MySQL 8.4
- Nginx
- Docker Compose

## Menjalankan dengan Docker

```bash
docker compose up -d --build
```

Aplikasi: [http://localhost:8080](http://localhost:8080)

MySQL (host): `localhost:3307`  
Database: `sepa_absensi` / user: `sepa` / password: `sepa_secret`

### Akun demo

| ID Karyawan | Password     |
|-------------|--------------|
| KRY001      | password123  |
| KRY002      | password123  |

## Fitur (sesuai SRS)

- Login / logout + kunci akun 15 menit setelah 5 gagal
- Absen masuk & pulang (waktu server WIB)
- Status **Tepat Waktu** / **Terlambat** (batas 08:00)
- Riwayat absensi + filter bulan/tahun + ringkasan + pagination
- Session idle 30 menit

## Perintah berguna

```bash
docker compose logs -f app
docker compose exec app php artisan migrate:fresh --seed
docker compose down
```

## Struktur

- `backend/` — kode Laravel
- `docker/` — Dockerfile PHP, Nginx, entrypoint
- `ui/` — prototipe UI statis (referensi)
- `docker-compose.yml` — orkestrasi layanan
