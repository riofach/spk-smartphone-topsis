@extends('layouts.app')

@section('title', 'Beranda')

@section('styles')
    <style>
        .hero {
            padding: 6rem 0;
            background-color: #1a1a2e;
            /* Solid color instead of gradient */
            color: white;
            margin-bottom: 3rem;
            border-radius: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            bottom: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background-color: rgba(109, 40, 217, 0.2);
            /* Light purple accent */
            filter: blur(70px);
            z-index: 0;
            border-radius: 50%;
        }

        .hero h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .hero p {
            position: relative;
            z-index: 1;
        }

        .hero-image {
            position: relative;
            z-index: 1;
            transition: transform 0.5s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .hero-image:hover {
            transform: translateY(-10px);
        }

        .feature-card {
            transition: all 0.3s;
            height: 100%;
            background-color: #16213e;
            /* Dark blue instead of gradient */
            border: none;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: #0f3460;
            /* Solid color */
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            color: #e94560;
            /* Accent color */
            transition: all 0.3s ease;
        }

        /* .feature-card:hover .feature-icon {
                                                        background: #e94560;
                                                        color: white;
                                                        transform: rotateY(180deg);
                                                    } */

        .btn-custom-primary {
            background-color: #e94560;
            border-color: #e94560;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom-primary:hover {
            background-color: #d63649;
            border-color: #d63649;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(233, 69, 96, 0.4);
        }

        .btn-custom-light {
            background-color: rgba(255, 255, 255, 0.9);
            color: #16213e;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom-light:hover {
            background-color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            position: relative;
            margin-bottom: 2rem;
            font-weight: 700;
            color: #e0e0e0;
        }

        .topsis-card {
            background-color: #16213e;
            border: none;
            transition: all 0.3s ease;
        }

        .topsis-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* Disclaimer Card Styling */
        .disclaimer-card {
            background-color: rgba(30, 41, 59, 0.9);
            border-left: 4px solid #e94560;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .disclaimer-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .disclaimer-content {
            color: #e2e8f0;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .disclaimer-content p {
            margin-bottom: 0.75rem;
        }

        .disclaimer-content strong {
            color: #60a5fa;
        }
    </style>
@endsection

@section('content')
    <div class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold mb-4">Sistem Pendukung Keputusan Pemilihan Smartphone</h1>
                    <p class="lead mb-4">Temukan smartphone terbaik sesuai dengan budget dan kebutuhan Anda menggunakan
                        metode TOPSIS (Technique for Order Preference by Similarity to Ideal Solution).</p>
                    </a>
                </div>
                <div class="col-lg-6 d-none d-lg-block text-center" data-aos="fade-left" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1621330396167-b3d451b9b83b?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Smartphone" class="img-fluid rounded-4 shadow hero-image" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mb-5">
            <div class="col-md-12 text-center" data-aos="fade-up">
                <h2 class="section-title">Bagaimana Cara Kerjanya?</h2>
                <p class="lead">SPK ini menggunakan metode TOPSIS untuk menemukan smartphone terbaik berdasarkan kriteria
                    yang Anda tentukan.</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card feature-card shadow h-100">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 class="fw-bold">1. Tentukan Budget</h4>
                        <p class="text-white">Masukkan range budget yang Anda miliki untuk membeli smartphone.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card feature-card shadow h-100">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-list-ol"></i>
                        </div>
                        <h4 class="fw-bold">2. Pilih Kriteria</h4>
                        <p class="text-white">Tentukan tingkat kepentingan kriteria seperti kamera, performa, desain, dan
                            baterai.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card feature-card shadow h-100">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4 class="fw-bold">3. Dapatkan Rekomendasi</h4>
                        <p class="text-white">Sistem akan menghitung dan merekomendasikan smartphone terbaik berdasarkan
                            metode TOPSIS.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-12 text-center" data-aos="fade-up">
                <div class="card topsis-card shadow p-4">
                    <h2 class="mb-4 fw-bold">Tentang Metode TOPSIS</h2>
                    <p>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution) adalah metode pengambilan
                        keputusan multi-kriteria. Metode ini memilih alternatif terbaik berdasarkan jarak terdekat dengan
                        solusi ideal positif dan terjauh dari solusi ideal negatif.</p>
                    <a href="{{ route('recommendation.form') }}" class="btn btn-custom-primary mt-3 mx-auto"
                        onclick="location.href='{{ route('recommendation.form') }}';" style="width: fit-content;">
                        <i class="fas fa-search me-2"></i> Coba Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Disclaimer Section -->
        <div class="row mb-5">
            <div class="col-md-12" data-aos="fade-up">
                <div class="card shadow disclaimer-card p-4">
                    <h3 class="mb-3 fw-bold"><i class="fas fa-info-circle me-2"></i>Disclaimer</h3>
                    <div class="disclaimer-content">
                        <p><strong>Tentang Penilaian Kriteria:</strong> Skor kriteria kamera, performa, baterai, dan desain
                            pada sistem ini adalah berdasarkan penilaian dari tim kami dengan mempertimbangkan spesifikasi
                            teknis dan pengalaman pengguna.</p>
                        <p><strong>Smartphone Rekomendasi:</strong> Sistem ini hanya merekomendasikan smartphone yang resmi
                            dijual di Indonesia dan dirilis dalam 2 tahun terakhir untuk memastikan rekomendasi yang relevan
                            dan up-to-date.</p>
                        <p><strong>Penggunaan Data:</strong> Hasil rekomendasi bersifat objektif berdasarkan kriteria yang
                            ditentukan, tetapi keputusan akhir tetap berada di tangan pengguna sesuai kebutuhan spesifik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Script untuk memastikan tombol berfungsi dengan baik
        document.addEventListener('DOMContentLoaded', function() {
            const recommendationButtons = document.querySelectorAll('a[href*="recommendation.form"]');

            recommendationButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('recommendation.form') }}";
                });
            });
        });
    </script>
@endsection
