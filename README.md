# Sistem Pendukung Keputusan Pemilihan Smartphone (TOPSIS)

Sistem Pendukung Keputusan (SPK) untuk membantu memilih smartphone berdasarkan budget dan kriteria kebutuhan menggunakan metode TOPSIS (Technique for Order Preference by Similarity to Ideal Solution).

## Kelompok

-   Fachrio Raditya
-   Miftahudin Aldi Saputra
-   Wahyu Nayoga

## Daftar Isi

-   [Fitur](#fitur)
-   [Teknologi](#teknologi)
-   [Optimasi Performa & SEO](#optimasi-performa--seo)
-   [Cara Instalasi](#cara-instalasi)
-   [Penggunaan](#penggunaan)
-   [Penjelasan Metode TOPSIS](#penjelasan-metode-topsis)
-   [Struktur Direktori](#struktur-direktori)

## Fitur

-   Pengguna dapat memasukkan range budget
-   Pengguna dapat memilih kriteria kebutuhan (kamera, performa, desain, baterai)
-   Sistem menghasilkan rekomendasi smartphone berdasarkan metode TOPSIS
-   Manajemen data smartphone (tambah, edit, hapus)
-   Upload gambar smartphone
-   Peringkat rekomendasi dengan tampilan visual
-   Manajemen data smartphone (CRUD)
-   Rekomendasi smartphone menggunakan metode TOPSIS berdasarkan preferensi pengguna
-   Penyimpanan gambar fleksibel (lokal atau Supabase)
-   Tampilan top 3 rekomendasi dengan ribbon khusus
-   Pembersihan otomatis smartphone yang sudah lebih dari 2 tahun
-   Pagination untuk tampilan daftar smartphone
-   Performa tinggi dengan caching dan optimasi SEO
-   Sitemap otomatis untuk meningkatkan indeksasi di mesin pencari

## Teknologi

-   PHP 8.2+
-   Laravel 12
-   PostgreSQL
-   Bootstrap 5
-   JavaScript/jQuery
-   Cache System Laravel
-   Lazy Loading Images
-   Browser Caching & GZIP Compression

## Optimasi Performa & SEO

Aplikasi ini telah dioptimasi untuk performa dan SEO dengan fitur-fitur berikut:

### Optimasi SEO

1. **Meta Tags yang Lengkap**

    - Meta description, keywords, dan author
    - Open Graph dan Twitter Card untuk sharing di sosial media
    - Canonical URL untuk mencegah konten duplikat

2. **Data Terstruktur (Schema.org)**

    - Markup data terstruktur untuk meningkatkan tampilan di hasil pencarian
    - Informasi website dengan action search

3. **Robots.txt & Sitemap**
    - Konfigurasi robots.txt untuk kontrol web crawler
    - Sitemap.xml otomatis yang diperbarui setiap 6 jam
    - URL prioritas sesuai kepentingan halaman

### Optimasi Performa

1. **Sistem Caching**

    - Model caching untuk mengurangi query database (dengan auto-invalidation)
    - Cache untuk hasil pencarian dan filter
    - Data caching sebagai pengganti view caching (menghindari serialisasi closure)

2. **Optimasi Asset**

    - Lazy loading untuk gambar menggunakan Intersection Observer
    - JavaScript dengan atribut defer untuk mencegah blocking rendering
    - Preconnect ke domain eksternal

3. **Server Optimization**

    - Browser caching dengan konfigurasi .htaccess
    - GZIP compression untuk mengurangi ukuran transfer
    - Cache headers untuk jenis file berbeda

4. **Task Scheduling**

    - Pembersihan cache otomatis terjadwal
    - Regenerasi sitemap secara periodik
    - Health check untuk memastikan website selalu online

5. **Command Kustom**
    - `php artisan cache:smartclear` untuk pembersihan cache
    - Pilihan pembersihan cache model, view, atau sitemap

## Cara Instalasi

Berikut langkah-langkah untuk menginstal dan menjalankan proyek:

### Prasyarat

-   PHP 8.2 atau lebih tinggi
-   Composer
-   PostgreSQL
-   Git

### Langkah Instalasi

1. **Clone repositori dari GitHub**

    ```bash
    git clone https://github.com/riofach/spk-smartphone-topsis.git
    cd spk-smartphone-topsis
    ```

2. **Instal dependencies**

    ```bash
    composer install
    ```

3. **Salin file .env**

    ```bash
    cp .env.example .env
    ```

4. **Konfigurasi database**

    Edit file `.env` dan sesuaikan konfigurasi database:

    ```
    DB_CONNECTION=pgsql
    DB_URL=tell the owner (using supabase)
    DB_PASSWORD=
    ```

5. **Generate application key**

    ```bash
    php artisan key:generate
    ```

6. **Jalankan migrasi dan seed**

    ```bash
    php artisan migrate --seed
    ```

7. **Buat direktori penyimpanan gambar**

    ```bash
    mkdir -p public/images/smartphones
    ```

8. **Optimalkan aplikasi**

    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

9. **Jalankan server development**

    ```bash
    php artisan serve
    ```

10. **Akses aplikasi**

    Buka browser dan kunjungi `http://localhost:8000`

## Penggunaan

### Pengguna Umum

1. **Halaman Utama**: Menampilkan informasi tentang cara kerja SPK TOPSIS
2. **Form Rekomendasi**: Masukkan range budget dan tingkat kepentingan kriteria
3. **Hasil Rekomendasi**: Lihat daftar smartphone yang direkomendasikan sesuai kebutuhan

### Admin

1. **Manajemen Smartphone**:

    - Lihat daftar smartphone: `/smartphones`
    - Tambah smartphone (URL tersembunyi): `/admin-add-smartphone`
    - Edit dan hapus smartphone

2. **Maintenance**:
    - Bersihkan cache: `php artisan cache:smartclear`
    - Regenerasi sitemap: `php artisan cache:smartclear --sitemap`
    - Bersihkan cache model: `php artisan cache:smartclear --model`

## Penjelasan Metode TOPSIS

TOPSIS (Technique for Order Preference by Similarity to Ideal Solution) adalah metode pengambilan keputusan multi-kriteria yang didasarkan pada konsep bahwa alternatif terbaik memiliki jarak terpendek dari solusi ideal positif dan jarak terjauh dari solusi ideal negatif.

### Proses Perhitungan TOPSIS

1. **Pembentukan Matriks Keputusan**

    Matriks keputusan dibentuk dari data smartphone dengan kriteria (kamera, performa, desain, baterai).

2. **Normalisasi Matriks**

    Normalisasi dilakukan dengan rumus:

    ```
    r_ij = x_ij / √(Σ(x_ij)²)
    ```

    di mana x_ij adalah nilai dari alternatif i untuk kriteria j.

3. **Perhitungan Matriks Berbobot**

    Matriks ternormalisasi berbobot dihitung dengan rumus:

    ```
    v_ij = r_ij × w_j
    ```

    di mana w_j adalah bobot untuk kriteria j yang dimasukkan oleh pengguna.

4. **Penentuan Solusi Ideal Positif dan Negatif**

    - Solusi ideal positif: nilai maksimum untuk kriteria benefit, nilai minimum untuk kriteria cost
    - Solusi ideal negatif: nilai minimum untuk kriteria benefit, nilai maksimum untuk kriteria cost

5. **Perhitungan Jarak dari Solusi Ideal**

    - Jarak ke solusi ideal positif:
        ```
        d_i⁺ = √Σ(v_ij - v_j⁺)²
        ```
    - Jarak ke solusi ideal negatif:
        ```
        d_i⁻ = √Σ(v_ij - v_j⁻)²
        ```

6. **Perhitungan Closeness Coefficient**

    Closeness coefficient dihitung dengan rumus:

    ```
    CC_i = d_i⁻ / (d_i⁺ + d_i⁻)
    ```

    Nilai CC_i berkisar antara 0 dan 1, di mana nilai yang lebih tinggi menunjukkan alternatif yang lebih baik.

7. **Peringkat Alternatif**

    Smartphone diurutkan berdasarkan nilai CC dari tertinggi ke terendah. Smartphone dengan nilai tertinggi adalah rekomendasi terbaik.

### Implementasi dalam Aplikasi

1. **Input Budget dan Kriteria**: Pengguna memasukkan range budget dan bobot untuk setiap kriteria
2. **Filtrasi Budget**: Sistem hanya mempertimbangkan smartphone dalam range budget
3. **Perhitungan TOPSIS**: Algoritma TOPSIS diterapkan pada smartphone yang tersisa
4. **Hasil**: Sistem menampilkan smartphone terurut berdasarkan nilai TOPSIS

## Struktur Direktori

-   `app/Http/Controllers/SmartphoneController.php`: Controller untuk manajemen smartphone dan rekomendasi
-   `app/Models/Smartphone.php`: Model untuk data smartphone dengan caching
-   `app/Models/Criteria.php`: Model untuk kriteria penilaian
-   `app/Services/TopsisService.php`: Implementasi algoritma TOPSIS
-   `app/Console/Commands/CacheClearCommand.php`: Command untuk membersihkan cache
-   `app/Console/Kernel.php`: Konfigurasi scheduled tasks
-   `database/migrations/`: Migrasi database
-   `database/seeders/`: Seeder untuk data awal
-   `resources/views/smartphones/`: Tampilan untuk manajemen smartphone
-   `resources/views/smartphones/recommendation.blade.php`: Form rekomendasi
-   `resources/views/smartphones/recommendation-results.blade.php`: Hasil rekomendasi
-   `public/images/smartphones/`: Penyimpanan gambar smartphone
-   `public/robots.txt`: Konfigurasi web crawler
-   `public/.htaccess`: Konfigurasi cache browser dan GZIP

## Penyimpanan Gambar

Aplikasi mendukung dua model penyimpanan gambar:

1. **Penyimpanan Lokal** - Default, menyimpan gambar di direktori public/images
2. **Supabase Storage** - Penyimpanan cloud yang menskalakan dengan lebih baik

Untuk konfigurasi dan migrasi ke Supabase, lihat:

-   [Panduan Supabase](README-SUPABASE.md) - Petunjuk integrasi dengan Supabase
-   [Panduan Migrasi](README-MIGRATION.md) - Cara memigrasikan gambar dari lokal ke Supabase

---

© 2025 SPK Pemilihan Smartphone TOPSIS. Dikembangkan sebagai proyek sistem pendukung keputusan.
