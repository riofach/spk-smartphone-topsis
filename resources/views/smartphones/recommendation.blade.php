@extends('layouts.app')

@section('title', 'Rekomendasi Smartphone')

@section('content')
    <div class="row" data-aos="fade-up">
        <div class="col-md-12">
            <h2 class="fw-bold text-gradient">Rekomendasi Smartphone</h2>
            <p class="text-white">Masukkan kriteria dan budget untuk mendapatkan rekomendasi smartphone terbaik berdasarkan
                metode TOPSIS.</p>
            <hr>
        </div>
    </div>

    <div class="row" data-aos="fade-up" data-aos-delay="100">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('recommendation.result') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="fw-bold mb-3"><i class="fas fa-money-bill-wave me-2"></i>Range Budget</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_budget" class="form-label">Budget Minimum (Rp) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('min_budget') is-invalid @enderror"
                                            id="min_budget" name="min_budget" value="{{ old('min_budget', 1000000) }}"
                                            min="0" required>
                                    </div>
                                    @error('min_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_budget" class="form-label">Budget Maksimum (Rp) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('max_budget') is-invalid @enderror"
                                            id="max_budget" name="max_budget" value="{{ old('max_budget', 10000000) }}"
                                            min="0" required>
                                    </div>
                                    @error('max_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="fw-bold mb-3"><i class="fas fa-sliders-h me-2"></i>Tingkat Kepentingan Kriteria
                                </h5>
                                <p class="text-white small">Berikan nilai kepentingan untuk setiap kriteria (1-10). Semakin
                                    tinggi nilai, semakin penting kriteria tersebut.</p>
                            </div>

                            @foreach ($criteria as $criterion)
                                <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                    <div class="card p-3">
                                        <label for="criteria_weights_{{ $criterion->code }}" class="form-label">
                                            <i
                                                class="fas fa-{{ $criterion->code == 'camera' ? 'camera' : ($criterion->code == 'performance' ? 'microchip' : ($criterion->code == 'design' ? 'palette' : 'battery-full')) }} me-2"></i>
                                            {{ $criterion->name }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="range" class="form-range flex-grow-1" min="1"
                                                max="10" step="1"
                                                id="criteria_weights_{{ $criterion->code }}_range"
                                                value="{{ old('criteria_weights.' . $criterion->code, 5) }}"
                                                oninput="document.getElementById('criteria_weights_{{ $criterion->code }}').value = this.value; updateRangeLabel('{{ $criterion->code }}', this.value)">
                                            <input type="number"
                                                class="form-control @error('criteria_weights.' . $criterion->code) is-invalid @enderror"
                                                id="criteria_weights_{{ $criterion->code }}"
                                                name="criteria_weights[{{ $criterion->code }}]"
                                                value="{{ old('criteria_weights.' . $criterion->code, 5) }}" min="1"
                                                max="10" style="width: 70px;"
                                                oninput="document.getElementById('criteria_weights_{{ $criterion->code }}_range').value = this.value; updateRangeLabel('{{ $criterion->code }}', this.value)"
                                                required>
                                        </div>
                                        <span class="mt-2 text-white small"
                                            id="range_label_{{ $criterion->code }}">Kepentingan Sedang</span>
                                        <small class="text-white mt-1">{{ $criterion->description }}</small>
                                        @error('criteria_weights.' . $criterion->code)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i> Cari Rekomendasi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg recommendation-card">
                    <div class="card-header bg-gradient text-white">
                        <h4 class="mb-0">Form Rekomendasi Smartphone</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Rekomendasi</h5>
                            <p>Sistem ini akan merekomendasikan smartphone berdasarkan preferensi Anda menggunakan metode
                                TOPSIS.</p>
                            <hr>
                            <p class="mb-0">
                                <i class="fas fa-check-circle me-1"></i> Skor kriteria (kamera, performa, baterai, desain)
                                berdasarkan penilaian tim kami.<br>
                                <i class="fas fa-check-circle me-1"></i> Hanya smartphone yang dirilis dalam 2 tahun
                                terakhir yang direkomendasikan.<br>
                                <i class="fas fa-check-circle me-1"></i> Smartphone yang direkomendasikan adalah yang resmi
                                dijual di Indonesia.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function updateRangeLabel(code, value) {
            const label = document.getElementById(`range_label_${code}`);
            const val = parseInt(value);

            if (val <= 2) {
                label.textContent = "Sangat Rendah";
                label.className = "mt-2 text-red small";
            } else if (val <= 4) {
                label.textContent = "Rendah";
                label.className = "mt-2 text-pink small";
            } else if (val <= 6) {
                label.textContent = "Sedang";
                label.className = "mt-2 text-white small";
            } else if (val <= 8) {
                label.textContent = "Tinggi";
                label.className = "mt-2 text-gradient small";
            } else {
                label.textContent = "Sangat Tinggi";
                label.className = "mt-2 text-gradient small fw-bold";
            }
        }

        // Initialize all range labels
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($criteria as $criterion)
                updateRangeLabel('{{ $criterion->code }}', document.getElementById(
                    'criteria_weights_{{ $criterion->code }}').value);
            @endforeach
        });
    </script>
@endsection
