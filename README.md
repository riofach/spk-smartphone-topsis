# Sistem Pendukung Keputusan Pemilihan Smartphone (TOPSIS)

Sistem Pendukung Keputusan (SPK) untuk membantu memilih smartphone berdasarkan budget dan kriteria kebutuhan menggunakan metode TOPSIS (Technique for Order Preference by Similarity to Ideal Solution).

## Kelompok

-   Fachrio Raditya
-   Miftahudin Aldi Saputra
-   Wahyu Nayoga

## Daftar Isi

-   [Fitur](#fitur)
-   [Teknologi](#teknologi)
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

## Teknologi

-   PHP 8.2+
-   Laravel 12
-   PostgreSQL
-   Bootstrap 5
-   JavaScript/jQuery

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

8. **Jalankan server development**

    ```bash
    php artisan serve
    ```

9. **Akses aplikasi**

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
-   `app/Models/Smartphone.php`: Model untuk data smartphone
-   `app/Models/Criteria.php`: Model untuk kriteria penilaian
-   `app/Services/TopsisService.php`: Implementasi algoritma TOPSIS
-   `database/migrations/`: Migrasi database
-   `database/seeders/`: Seeder untuk data awal
-   `resources/views/smartphones/`: Tampilan untuk manajemen smartphone
-   `resources/views/smartphones/recommendation.blade.php`: Form rekomendasi
-   `resources/views/smartphones/recommendation-results.blade.php`: Hasil rekomendasi
-   `public/images/smartphones/`: Penyimpanan gambar smartphone

---

© 2025 SPK Pemilihan Smartphone TOPSIS. Dikembangkan sebagai proyek sistem pendukung keputusan.
