# Panduan Migrasi Gambar Smartphone ke Supabase

Dokumen ini memberikan panduan lengkap tentang cara melakukan migrasi gambar smartphone dari penyimpanan lokal ke Supabase Storage.

## Prasyarat

Sebelum memulai proses migrasi, pastikan hal-hal berikut sudah dikonfigurasi dengan benar:

1. Supabase project sudah dibuat dan dikonfigurasi
2. Bucket `smartphones` sudah ada di Supabase Storage
3. File `.env` sudah berisi nilai yang benar untuk:
    ```
    SUPABASE_URL=https://your-project-ref.supabase.co
    SUPABASE_KEY=your-supabase-api-key
    SUPABASE_BUCKET=smartphones
    ```

## Cara Menggunakan Command Migrasi

Aplikasi menyediakan command Artisan khusus untuk memudahkan proses migrasi gambar smartphone dari penyimpanan lokal ke Supabase:

```bash
# Menjalankan migrasi dengan konfirmasi interaktif
php artisan smartphones:migrate-images

# Menjalankan migrasi tanpa konfirmasi (untuk deployment otomatis)
php artisan smartphones:migrate-images --force
```

### Apa yang Dilakukan Command Ini?

Command migrasi akan:

1. Menemukan semua smartphone dengan gambar yang disimpan secara lokal (bukan di Supabase)
2. Mengupload setiap gambar ke Supabase Storage dengan nama yang dibuat dari nama smartphone
3. Memperbarui kolom `image_url` di database dengan URL Supabase yang baru
4. Menawarkan opsi untuk menghapus gambar lokal yang berhasil dimigrasikan

### Output Command

Command akan menampilkan:

-   Jumlah smartphone yang ditemukan dengan gambar lokal
-   Progress bar selama proses migrasi
-   Pesan sukses atau error untuk setiap smartphone
-   Ringkasan akhir (jumlah sukses dan gagal)
-   Opsi untuk menghapus gambar lokal

## Migrasi Manual (Alternatif)

Jika Anda perlu melakukan migrasi secara manual, gunakan langkah-langkah berikut:

1. Identifikasi smartphone dengan gambar lokal:

    ```php
    $smartphones = Smartphone::whereRaw("image_url NOT LIKE '%supabase%'")->get();
    ```

2. Untuk setiap smartphone, upload gambar ke Supabase menggunakan service:
    ```php
    $service = app(SmartphoneImageService::class);
    $newImageUrl = $service->processAndUpload($uploadedFile, $smartphone->name);
    $smartphone->update(['image_url' => $newImageUrl]);
    ```

## Pemecahan Masalah

### Command Tidak Muncul

Jika command tidak muncul saat menjalankan `php artisan list`, pastikan:

1. File `app/Console/Commands/MigrateSmartphoneImages.php` ada
2. Command terdaftar di `app/Console/Kernel.php` dalam property `$commands`
3. Jalankan `php artisan optimize:clear` untuk membersihkan cache

### Error Supabase

Jika terjadi error saat mengupload ke Supabase:

1. Periksa kredensial Supabase di `.env`
2. Pastikan bucket `smartphones` sudah dibuat
3. Periksa izin bucket (sebaiknya set ke publik)
4. Periksa pengaturan CORS di Supabase jika diperlukan

### File Tidak Ditemukan

Jika ada error "File not found", pastikan:

1. Path gambar di database benar (relatif terhadap `public_path()`)
2. File benar-benar ada di lokasi tersebut
3. Webserver memiliki izin untuk membaca file tersebut
