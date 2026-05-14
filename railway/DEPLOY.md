# Deploy Railway

Project ini sudah dipersiapkan untuk Railway. Supaya deploy stabil dan perubahan data tidak tertimpa setiap restart, gunakan alur berikut.

## Yang perlu kamu siapkan

1. Satu service app dari repo GitHub ini
2. Satu service `MySQL` di Railway
3. Variables dari file `railway/variables.railway.example`
4. Pre-deploy command:

```bash
sh ./railway/init-app.sh
```

## Langkah singkat

1. Push project ke GitHub.
2. Buat project baru di Railway dari repo GitHub.
3. Tambahkan service `MySQL`.
4. Buka service app lalu isi variables memakai template `railway/variables.railway.example`.
5. Di service app, isi `Pre-deploy Command` dengan:

```bash
sh ./railway/init-app.sh
```

6. Deploy.
7. Jika deploy pertama dan kamu ingin data kategori, peserta, dan soal langsung masuk, set:

```env
RUN_DB_SEED=true
```

8. Setelah deploy pertama sukses, ubah lagi:

```env
RUN_DB_SEED=false
```

Ini penting agar data yang nanti diedit dari Filament tidak ditimpa lagi pada deploy berikutnya.

## Variable yang wajib diisi manual

- `APP_KEY`
- `APP_URL`

`APP_KEY` bisa dibuat dari lokal:

```bash
php artisan key:generate --show
```

`APP_URL` diisi setelah kamu sudah membuat domain gratis Railway.

## Variable yang wajib dihubungkan

```env
DB_URL=${{MySQL.MYSQL_URL}}
```

Pastikan service database memang bernama `MySQL` di Railway.

## Setelah domain jadi

1. Generate domain gratis Railway dari service app.
2. Salin domain `*.up.railway.app`.
3. Isi `APP_URL` dengan domain tersebut.
4. Redeploy sekali lagi.

## Pemeriksaan akhir

- halaman utama terbuka
- kategori muncul
- nomor soal muncul
- modal soal bekerja
- `/admin` bisa diakses
