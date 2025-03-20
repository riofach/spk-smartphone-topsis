@extends('layouts.app')

@section('title', 'Beranda')

@section('styles')
    <style>
        .hero {
            padding: 5rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 3rem;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .feature-card {
            transition: all 0.3s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #0d6efd;
        }
    </style>
@endsection

@section('content')
    <div class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Sistem Pendukung Keputusan Pemilihan Smartphone</h1>
                    <p class="lead mb-4">Temukan smartphone terbaik sesuai dengan budget dan kebutuhan Anda menggunakan
                        metode TOPSIS (Technique for Order Preference by Similarity to Ideal Solution).</p>
                    <a href="{{ route('recommendation.form') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-search"></i> Mulai Cari Rekomendasi
                    </a>
                </div>
                <div class="col-lg-6 d-none d-lg-block text-center">
                    <img src="https://cdn.pixabay.com/photo/2017/01/06/13/50/smartphone-1957740_960_720.jpg" alt="Smartphone"
                        class="img-fluid rounded shadow" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mb-5">
            <div class="col-md-12 text-center">
                <h2 class="mb-4">Bagaimana Cara Kerjanya?</h2>
                <p class="lead">SPK ini menggunakan metode TOPSIS untuk menemukan smartphone terbaik berdasarkan kriteria
                    yang Anda tentukan.</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4>1. Tentukan Budget</h4>
                        <p>Masukkan range budget yang Anda miliki untuk membeli smartphone.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-list-ol"></i>
                        </div>
                        <h4>2. Pilih Kriteria</h4>
                        <p>Tentukan tingkat kepentingan kriteria seperti kamera, performa, desain, dan tampilan.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>3. Dapatkan Rekomendasi</h4>
                        <p>Sistem akan menghitung dan merekomendasikan smartphone terbaik berdasarkan metode TOPSIS.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-12 text-center">
                <h2 class="mb-4">Tentang Metode TOPSIS</h2>
                <p>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution) adalah metode pengambilan
                    keputusan multi-kriteria. Metode ini memilih alternatif terbaik berdasarkan jarak terdekat dengan solusi
                    ideal positif dan terjauh dari solusi ideal negatif.</p>
                <a href="{{ route('recommendation.form') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-search"></i> Coba Sekarang
                </a>
            </div>
        </div>
    </div>
@endsection
