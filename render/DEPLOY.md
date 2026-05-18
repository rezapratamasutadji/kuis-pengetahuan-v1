# Deploy ke Render

Project ini sudah disiapkan untuk Render dengan runtime Docker dan database PostgreSQL dari Supabase.

## Yang sudah disiapkan

- `Dockerfile` untuk Laravel + Filament + Vite
- `render.yaml` untuk blueprint Render
- `render/start.sh` untuk startup container, migrasi, dan seed opsional
- `render/.env.render.example` untuk daftar env production

## Langkah deploy

1. Push project ini ke GitHub.
2. Di Render, pilih `New` -> `Blueprint`.
3. Pilih repository GitHub project ini.
4. Render akan membaca `render.yaml` otomatis.
5. Isi env yang bertanda `sync: false`, terutama:
   - `APP_KEY`
   - `APP_URL`
   - `DB_HOST`
   - `DB_USERNAME`
   - `DB_PASSWORD`
6. Gunakan kredensial Supabase PostgreSQL, sebaiknya dari Session Pooler jika environment kamu IPv4-only.
7. Deploy.

## Catatan env

- `RUN_MIGRATIONS=true` akan menjalankan `php artisan migrate --force` saat container start.
- `RUN_DB_SEED=false` disarankan untuk production agar data Filament tidak tertimpa.
- Jika ingin seed awal sekali saat deploy pertama, ubah sementara:

```env
RUN_DB_SEED=true
```

Lalu setelah data masuk, kembalikan ke:

```env
RUN_DB_SEED=false
```

## Pemeriksaan akhir

- halaman utama tampil
- asset CSS/JS termuat
- `/admin` bisa diakses
- tabel Supabase sudah terisi
