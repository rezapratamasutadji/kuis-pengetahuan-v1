# Kuis Cakrawala

Kuis Cakrawala adalah aplikasi kuis pengetahuan umum berbasis Laravel, Filament, React, dan Vite. Halaman utama menampilkan peserta, kategori, nomor soal bertingkat kesulitan, serta penilaian poin berdasarkan level soal.

## Fitur

- 10 kategori kuis
- 25 nomor soal per kategori
- Tingkat kesulitan:
  - 1-12 `Easy` `+10 poin`
  - 13-20 `Medium` `+15 poin`
  - 21-25 `Hard` `+30 poin`
- Peserta maksimal 4 orang tampil di halaman utama
- Soal yang sudah dijawab akan terkunci sampai tombol segarkan ditekan atau halaman dimuat ulang
- Panel admin Filament untuk kelola peserta, kategori, dan soal

## Stack

- Laravel 12
- Filament 3.3
- React 19
- Vite 7
- SQLite untuk lokal
- MySQL disarankan untuk Railway

## Menjalankan Lokal

1. Install dependency:

```bash
composer install
npm install
```

2. Salin environment lalu generate key:

```bash
copy .env.example .env
php artisan key:generate
```

3. Untuk mode lokal cepat, pakai SQLite:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate:fresh --seed --force
```

4. Jalankan server Laravel:

```bash
php artisan serve
```

5. Jalankan salah satu mode frontend:

Mode development:
```bash
npm run dev
```

Mode build:
```bash
npm run build
```

## Catatan Penting Frontend

- Jika `public/hot` ada, Laravel akan membaca asset dari Vite dev server.
- Jika `public/hot` tidak ada, Laravel akan membaca asset hasil build di `public/build`.
- Jadi kalau kamu sedang tidak menjalankan `npm run dev`, setiap perubahan pada `resources/js/app.jsx` atau `resources/css/app.css` harus diikuti `npm run build`.

## Admin Filament

Panel admin tersedia di:

```text
/admin
```

User admin bisa dibuat atau diubah dari seeder / artisan sesuai kebutuhan project.

## Deploy ke Railway

Panduan ringkas Railway ada di:

- [railway/DEPLOY.md](railway/DEPLOY.md)
- [railway/variables.railway.example](railway/variables.railway.example)

Ringkasnya:

1. Deploy repo ini ke Railway.
2. Tambahkan service `MySQL`.
3. Isi variables memakai template `railway/variables.railway.example`.
4. Isi `Pre-deploy Command` dengan:

```bash
sh ./railway/init-app.sh
```

5. Isi `Build Command` dengan:

```bash
sh ./railway/build-app.sh
```

6. Saat deploy pertama saja, set:

```env
RUN_DB_SEED=true
```

7. Setelah data awal masuk, kembalikan:

```env
RUN_DB_SEED=false
```

Ini membuat project lebih aman untuk production karena deploy berikutnya tidak akan mengulang seed dan menimpa data Filament.

## Struktur Penting

- `resources/js/app.jsx` untuk frontend React utama
- `resources/css/app.css` untuk styling
- `resources/views/app.blade.php` untuk mount React
- `app/Filament` untuk panel admin
- `database/seeders` untuk data awal kuis

## Lisensi

Project ini mengikuti lisensi MIT dari basis Laravel yang digunakan.
