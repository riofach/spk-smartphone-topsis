@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Smartphone')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8" data-aos="fade-right">
            <h2 class="fw-bold text-gradient">Hasil Rekomendasi Smartphone</h2>
            <p class="text-white">Hasil perhitungan TOPSIS berdasarkan kriteria yang Anda pilih.</p>
        </div>
        <div class="col-md-4 text-end" data-aos="fade-left">
            <a href="{{ route('recommendation.form') }}" class="btn btn-primary">
                <i class="fas fa-redo me-2"></i> Cari Rekomendasi Lain
            </a>
        </div>
    </div>

    <div class="row mb-4" data-aos="fade-up">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-dark">
                    <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Kriteria Pencarian</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong><i class="fas fa-money-bill-wave me-2"></i>Budget:</strong></p>
                            <p class="text-gradient fw-bold">Rp {{ number_format($min_budget, 0, ',', '.') }} - Rp
                                {{ number_format($max_budget, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-1"><strong><i class="fas fa-sliders-h me-2"></i>Bobot Kriteria:</strong></p>
                            <div class="row">
                                @foreach ($criteria_weights as $code => $weight)
                                    <div class="col-md-3 mb-2">
                                        <span class="badge bg-primary p-2">
                                            <i
                                                class="fas fa-{{ $code == 'camera' ? 'camera' : ($code == 'performance' ? 'microchip' : ($code == 'design' ? 'palette' : 'battery-full')) }} me-1"></i>
                                            {{ ucfirst($code) }}: {{ $weight }}
                                        </span>
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
        <div class="alert alert-info" data-aos="fade-up">
            <p class="mb-0"><i class="fas fa-info-circle me-2"></i>Tidak ditemukan smartphone yang sesuai dengan kriteria
                Anda. Silakan ubah kriteria pencarian.</p>
        </div>
    @else
        <!-- Baris pertama: 3 smartphone teratas -->
        <div class="row" data-aos="fade-up">
            @foreach ($recommendations as $index => $recommendation)
                @php $smartphone = $recommendation['smartphone']; @endphp
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div class="card h-100 {{ $index === 0 ? 'border-primary' : '' }}" id="card-{{ $index }}">
                        @if ($index === 0)
                            <div class="ribbon ribbon-top-right"><span>Terbaik</span></div>
                        @elseif ($index === 1)
                            <div class="ribbon ribbon-silver ribbon-top-right"><span>Terbaik Kedua</span></div>
                        @elseif ($index === 2)
                            <div class="ribbon ribbon-bronze ribbon-top-right"><span>Terbaik Ketiga</span></div>
                        @endif

                        <div class="card-header {{ $index === 0 ? 'bg-primary text-white' : 'bg-dark' }}">
                            <h5 class="card-title mb-0">
                                <div class="medal-icon medal-{{ $index }}">
                                    <i
                                        class="fas fa-medal me-2 {{ $index === 0 ? 'text-warning' : ($index === 1 ? 'text-light' : 'text-danger') }}"></i>
                                </div>
                                {{ $smartphone->name }}
                            </h5>
                        </div>

                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <img src="{{ $smartphone->image_url }}" class="card-img-top" alt="{{ $smartphone->name }}"
                                style="height: 100%; object-fit: contain; transition: transform 0.5s ease;">
                        </div>

                        <div class="card-body">
                            <h5 class="card-title text-gradient mb-3 fw-bold">Rp
                                {{ number_format($smartphone->price, 0, ',', '.') }}</h5>

                            <p class="card-text">{{ $smartphone->description ?: 'Tidak ada deskripsi' }}</p>

                            <div class="mt-3">
                                <p class="mb-1 fw-bold">
                                    <i class="fas fa-star me-2 text-warning"></i>
                                    Skor TOPSIS: <span
                                        class="text-gradient">{{ number_format($recommendation['score'] * 100, 2) }}%</span>
                                </p>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $recommendation['score'] * 100 }}%"
                                        aria-valuenow="{{ $recommendation['score'] * 100 }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>

                                <p class="mb-1 fw-bold"><i class="fas fa-info-circle me-2"></i>Spesifikasi:</p>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-camera me-2"></i>Kamera</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->camera_score, 1) }}/10</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-microchip me-2"></i>Performa</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->performance_score, 1) }}/10</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-palette me-2"></i>Desain</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->design_score, 1) }}/10</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-battery-full me-2"></i>Baterai</span>
                                        <span
                                            class="badge bg-secondary">{{ number_format($smartphone->battery_score, 1) }}/10</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('styles')
    <style>
        /* Medal Icons */
        .medal-icon {
            display: inline-block;
            position: relative;
        }

        .medal-icon .fa-medal {
            font-size: 1.2rem;
        }

        .medal-0 .fa-medal {
            color: #ffd700 !important;
            /* Gold */
            filter: drop-shadow(0 0 3px rgba(255, 215, 0, 0.6));
        }

        .medal-1 .fa-medal {
            color: #c0c0c0 !important;
            /* Silver */
            filter: drop-shadow(0 0 3px rgba(192, 192, 192, 0.6));
        }

        .medal-2 .fa-medal {
            color: #cd7f32 !important;
            /* Bronze */
            filter: drop-shadow(0 0 3px rgba(205, 127, 50, 0.6));
        }

        /* Ribbon styling */
        .ribbon {
            width: 150px;
            height: 150px;
            overflow: hidden;
            position: absolute;
            z-index: 10;
        }

        .ribbon span {
            position: absolute;
            display: block;
            width: 225px;
            padding: 8px 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            transform: rotate(45deg);
            animation: pulse 2s infinite;
        }

        .ribbon-silver span {
            background: linear-gradient(135deg, #c0c0c0, #a9a9a9);
        }

        .ribbon-bronze span {
            background: linear-gradient(135deg, #cd7f32, #a05a2c);
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

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(109, 40, 217, 0.5);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(109, 40, 217, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(109, 40, 217, 0);
            }
        }

        #card-0 {
            animation: glow 2s infinite alternate;
            border-color: #ffd700 !important;
        }

        #card-1 {
            border-color: #c0c0c0;
            animation: glowSilver 2s infinite alternate;
        }

        #card-2 {
            border-color: #cd7f32;
            animation: glowBronze 2s infinite alternate;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 5px -5px #ffd700;
            }

            to {
                box-shadow: 0 0 20px 5px #ffd700;
            }
        }

        @keyframes glowSilver {
            from {
                box-shadow: 0 0 5px -5px #c0c0c0;
            }

            to {
                box-shadow: 0 0 15px 3px #c0c0c0;
            }
        }

        @keyframes glowBronze {
            from {
                box-shadow: 0 0 5px -5px #cd7f32;
            }

            to {
                box-shadow: 0 0 12px 2px #cd7f32;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effect to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const img = this.querySelector('.card-img-top');
                    if (img) {
                        img.style.transform = 'scale(1.1)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    const img = this.querySelector('.card-img-top');
                    if (img) {
                        img.style.transform = 'scale(1)';
                    }
                });
            });
        });
    </script>
@endsection
