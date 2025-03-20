@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Smartphone')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Hasil Rekomendasi Smartphone</h2>
            <p class="text-muted">Hasil perhitungan TOPSIS berdasarkan kriteria yang Anda pilih.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('recommendation.form') }}" class="btn btn-primary">
                <i class="fas fa-redo"></i> Cari Rekomendasi Lain
            </a>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Kriteria Pencarian</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Budget:</strong></p>
                            <p>Rp {{ number_format($min_budget, 0, ',', '.') }} - Rp
                                {{ number_format($max_budget, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-1"><strong>Bobot Kriteria:</strong></p>
                            <div class="row">
                                @foreach ($criteria_weights as $code => $weight)
                                    <div class="col-md-3 mb-2">
                                        <span class="badge bg-primary">{{ ucfirst($code) }}: {{ $weight }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($recommendations->isEmpty())
        <div class="alert alert-info">
            <p class="mb-0">Tidak ditemukan smartphone yang sesuai dengan kriteria Anda. Silakan ubah kriteria pencarian.
            </p>
        </div>
    @else
        <div class="row">
            @foreach ($recommendations as $index => $recommendation)
                @php $smartphone = $recommendation['smartphone']; @endphp
                <div class="col-md-4 mb-4">
                    <div class="card h-100 {{ $index === 0 ? 'border-primary' : '' }}">
                        @if ($index === 0)
                            <div class="ribbon ribbon-top-right"><span>Terbaik</span></div>
                        @endif

                        <div class="card-header {{ $index === 0 ? 'bg-primary text-white' : 'bg-light' }}">
                            <h5 class="card-title mb-0">{{ $smartphone->name }}</h5>
                        </div>

                        @if ($smartphone->image_url)
                            <img src="{{ $smartphone->image_url }}" class="card-img-top p-3" alt="{{ $smartphone->name }}"
                                style="height: 200px; object-fit: contain;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                style="height: 200px;">
                                <i class="fas fa-mobile-alt fa-4x text-muted"></i>
                            </div>
                        @endif

                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Rp
                                {{ number_format($smartphone->price, 0, ',', '.') }}</h5>

                            <p class="card-text">{{ $smartphone->description ?: 'Tidak ada deskripsi' }}</p>

                            <div class="mt-3">
                                <p class="mb-1"><strong>Skor TOPSIS:
                                        {{ number_format($recommendation['score'] * 100, 2) }}%</strong></p>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $recommendation['score'] * 100 }}%"
                                        aria-valuenow="{{ $recommendation['score'] * 100 }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>

                                <p class="mb-1"><strong>Spesifikasi:</strong></p>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Kamera</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->camera_score, 1) }}/10</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Performa</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->performance_score, 1) }}/10</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Desain</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->design_score, 1) }}/10</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Baterai</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->battery_score, 1) }}/10</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                @if (($index + 1) % 3 == 0)
        </div>
        <div class="row">
    @endif
    @endforeach
    </div>
    @endif
@endsection

@section('styles')
    <style>
        .ribbon {
            width: 150px;
            height: 150px;
            overflow: hidden;
            position: absolute;
        }

        .ribbon::before,
        .ribbon::after {
            position: absolute;
            z-index: -1;
            content: '';
            display: block;
            border: 5px solid #0d6efd;
        }

        .ribbon span {
            position: absolute;
            display: block;
            width: 225px;
            padding: 8px 0;
            background-color: #0d6efd;
            box-shadow: 0 5px 10px rgba(0, 0, 0, .1);
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
            text-transform: uppercase;
            text-align: center;
        }

        .ribbon-top-right {
            top: -10px;
            right: -10px;
        }

        .ribbon-top-right::before,
        .ribbon-top-right::after {
            border-top-color: transparent;
            border-right-color: transparent;
        }

        .ribbon-top-right::before {
            top: 0;
            left: 0;
        }

        .ribbon-top-right::after {
            bottom: 0;
            right: 0;
        }

        .ribbon-top-right span {
            left: -25px;
            top: 30px;
            transform: rotate(45deg);
        }
    </style>
@endsection
