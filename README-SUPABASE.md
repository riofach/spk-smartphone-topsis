# Integrasi Supabase Storage untuk Gambar Smartphone

## Pengenalan

Aplikasi SPK-HP-TOPSIS sekarang mendukung penyimpanan gambar di Supabase Storage untuk mengelola gambar smartphone dengan lebih efisien. Fitur ini meningkatkan performa dan skalabilitas aplikasi dengan menyimpan gambar di cloud storage, bukan di server lokal.

## Fitur Utama

1. **Kompresi Gambar Otomatis**

    - Gambar yang diunggah dengan ukuran lebih dari 250KB akan dikompresi secara otomatis
    - Kualitas gambar akan diturunkan secara bertahap hingga ukuran file di bawah batas maksimum
    - Batas kompresi minimal adalah 30% untuk mempertahankan kualitas gambar yang wajar

2. **Penyimpanan di Supabase Storage**
    - Gambar disimpan di bucket `smartphones` di Supabase Storage
    - URL gambar yang disimpan di database adalah URL publik dari Supabase
    - Penghapusan data smartphone secara otomatis juga akan menghapus gambar dari Supabase

## Konfigurasi

### 1. Buat Storage Bucket di Supabase

1. Login ke dashboard Supabase di [https://app.supabase.com](https://app.supabase.com)
2. Pilih project yang ingin digunakan
3. Klik "Storage" dari menu sidebar
4. Klik "Create bucket"
5. Masukkan nama bucket: `smartphones`
6. Centang "Public bucket" untuk memungkinkan akses publik ke gambar
7. Klik "Create bucket"

### 2. Konfigurasi `.env`

Tambahkan konfigurasi berikut ke file `.env` Anda:

```
SUPABASE_URL=https://your-project-url.supabase.co
SUPABASE_KEY=your-service-role-key-or-anon-key
SUPABASE_BUCKET=smartphones
```

Untuk mendapatkan nilai-nilai ini:

-   SUPABASE_URL: URL project Supabase Anda (tersedia di Dashboard > Settings > API)
-   SUPABASE_KEY: Service Role Key atau Anon Key (tersedia di Dashboard > Settings > API)
-   SUPABASE_BUCKET: Nama bucket yang telah dibuat (defaultnya `smartphones`)

### 3. Konfigurasi CORS (jika diperlukan)

Jika mengalami masalah CORS saat mengakses gambar:

1. Buka Supabase Dashboard
2. Navigasi ke Settings > API > CORS
3. Tambahkan domain aplikasi Anda ke allowed origins
4. Pastikan `*` (wildcard) ditambahkan jika aplikasi diakses dari berbagai domain

## Penggunaan Service

Service telah diintegrasikan dengan SmartphoneController secara otomatis. Namun jika ingin menggunakan secara manual di controller lain:

```php
// Di controller
use App\Services\SmartphoneImageService;

class OtherController extends Controller
{
    protected $smartphoneImageService;

    public function __construct(SmartphoneImageService $smartphoneImageService)
    {
        $this->smartphoneImageService = $smartphoneImageService;
    }

    public function example(Request $request)
    {
        if ($request->hasFile('image')) {
            // Upload gambar ke Supabase
            $imageUrl = $this->smartphoneImageService->processAndUpload(
                $request->file('image'),
                'nama-untuk-file'
            );

            // Gunakan URL untuk disimpan ke database
            // ...
        }
    }
}
```

## Migrasi dari Penyimpanan Lokal

Sistem saat ini mendukung penyimpanan lokal dan Supabase secara bersamaan. Jika ada gambar yang sudah disimpan di lokal, aplikasi akan tetap dapat menampilkannya. Gambar baru akan disimpan di Supabase.

### Menggunakan Command Migrasi Otomatis

Aplikasi menyediakan command Artisan untuk memigrasikan gambar dari penyimpanan lokal ke Supabase:

```bash
# Migrasi dengan konfirmasi
php artisan smartphones:migrate-images

# Migrasi tanpa konfirmasi (untuk deployment otomatis)
php artisan smartphones:migrate-images --force
```

Command ini akan:

1. Mengidentifikasi semua smartphone dengan gambar lokal
2. Mengupload gambar tersebut ke Supabase dengan kompresi otomatis jika diperlukan
3. Memperbarui URL gambar di database
4. Memberikan opsi untuk menghapus file lokal yang berhasil dimigrasikan

### Migrasi Manual

Anda juga dapat melakukan migrasi manual dengan:

1. Mengambil data dari penyimpanan lokal
2. Mengunggahnya ke Supabase menggunakan service
3. Mengupdate URL di database

## Troubleshooting

### Gambar Tidak Muncul

-   Pastikan bucket diatur sebagai public
-   Verifikasi URL yang disimpan di database
-   Periksa log aplikasi untuk pesan error

### Error Upload

-   Pastikan SUPABASE_KEY memiliki izin untuk menulis ke bucket
-   Periksa batas ukuran file di Supabase (defaultnya 50MB)
-   Pastikan format gambar didukung
