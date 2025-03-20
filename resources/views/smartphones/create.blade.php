@extends('layouts.app')

@section('title', 'Tambah Smartphone')

@section('content')
    <div class="row">
        <div class="col-md-12" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold text-gradient">Tambah Smartphone Baru</h2>
                <a href="{{ route('smartphones.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" data-aos="fade-up" data-aos-delay="100">
            <div class="card shadow">
                <div class="card-body p-4">
                    <form action="{{ route('smartphones.store') }}" method="POST" enctype="multipart/form-data"
                        id="smartphoneForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6" data-aos="fade-right" data-aos-delay="200">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-mobile-alt me-2"></i>Nama Smartphone <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-tag me-2"></i>Harga (Rp) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                                            id="price" name="price" value="{{ old('price') }}" min="0"
                                            required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">
                                        <i class="fas fa-image me-2"></i>Gambar (PNG, Maks. 1MB)
                                    </label>
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            id="image" name="image" accept=".png" onchange="previewImage(this)">
                                    </div>
                                    <div class="mt-2 text-center d-none" id="imagePreviewContainer">
                                        <img id="imagePreview" src="#" alt="Preview" class="img-thumbnail"
                                            style="max-height: 150px;">
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-white">Format yang diizinkan: PNG dengan ukuran maksimal 1MB</small>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left me-2"></i>Deskripsi
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" data-aos="fade-left" data-aos-delay="300">
                                <div class="card mb-4">
                                    <div class="card-header bg-dark">
                                        <h5 class="mb-0 text-gradient">Skor Kriteria</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="camera_score" class="form-label">
                                                <i class="fas fa-camera me-2"></i>Skor Kamera (1-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="camera_score_range"
                                                    value="{{ old('camera_score', 5) }}"
                                                    oninput="updateScore('camera_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('camera_score') is-invalid @enderror"
                                                    id="camera_score" name="camera_score"
                                                    value="{{ old('camera_score', 5) }}" min="0" max="10"
                                                    step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('camera_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="camera_score_progress" class="progress-bar" role="progressbar"
                                                    style="width: 50%;" aria-valuenow="5" aria-valuemin="0"
                                                    aria-valuemax="10"></div>
                                            </div>
                                            @error('camera_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="performance_score" class="form-label">
                                                <i class="fas fa-microchip me-2"></i>Skor Performa (1-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="performance_score_range"
                                                    value="{{ old('performance_score', 5) }}"
                                                    oninput="updateScore('performance_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('performance_score') is-invalid @enderror"
                                                    id="performance_score" name="performance_score"
                                                    value="{{ old('performance_score', 5) }}" min="0"
                                                    max="10" step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('performance_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="performance_score_progress" class="progress-bar"
                                                    role="progressbar" style="width: 50%;" aria-valuenow="5"
                                                    aria-valuemin="0" aria-valuemax="10"></div>
                                            </div>
                                            @error('performance_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="design_score" class="form-label">
                                                <i class="fas fa-palette me-2"></i>Skor Desain (1-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="design_score_range"
                                                    value="{{ old('design_score', 5) }}"
                                                    oninput="updateScore('design_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('design_score') is-invalid @enderror"
                                                    id="design_score" name="design_score"
                                                    value="{{ old('design_score', 5) }}" min="0" max="10"
                                                    step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('design_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="design_score_progress" class="progress-bar" role="progressbar"
                                                    style="width: 50%;" aria-valuenow="5" aria-valuemin="0"
                                                    aria-valuemax="10"></div>
                                            </div>
                                            @error('design_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="battery_score" class="form-label">
                                                <i class="fas fa-battery-full me-2"></i>Skor Baterai (1-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="battery_score_range"
                                                    value="{{ old('battery_score', 5) }}"
                                                    oninput="updateScore('battery_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('battery_score') is-invalid @enderror"
                                                    id="battery_score" name="battery_score"
                                                    value="{{ old('battery_score', 5) }}" min="0" max="10"
                                                    step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('battery_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="battery_score_progress" class="progress-bar" role="progressbar"
                                                    style="width: 50%;" aria-valuenow="5" aria-valuemin="0"
                                                    aria-valuemax="10"></div>
                                            </div>
                                            @error('battery_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card radar-chart-card">
                                    <div class="card-header bg-dark">
                                        <h5 class="mb-0 text-gradient">Visualisasi Skor</h5>
                                    </div>
                                    <div class="card-body text-center p-4">
                                        <canvas id="radarChart" width="250" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" data-aos="fade-up">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-4 py-2">
                                    <i class="fas fa-save me-2"></i> Simpan Smartphone
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Preview image when uploaded
        function previewImage(input) {
            const container = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('d-none');
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = "#";
                container.classList.add('d-none');
            }
        }

        // Update score when range is changed
        function updateScore(fieldId, value) {
            document.getElementById(fieldId).value = value;
            updateScoreProgress(fieldId, value);
            updateRadarChart();
        }

        // Update range when score is changed
        function updateScoreRange(fieldId, value) {
            document.getElementById(fieldId + '_range').value = value;
            updateScoreProgress(fieldId, value);
            updateRadarChart();
        }

        // Update progress bar
        function updateScoreProgress(fieldId, value) {
            const progressBar = document.getElementById(fieldId + '_progress');
            const percentage = (value / 10) * 100;
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', value);
        }

        // Radar Chart
        let radarChart;

        function initRadarChart() {
            const ctx = document.getElementById('radarChart').getContext('2d');

            const cameraScore = parseFloat(document.getElementById('camera_score').value || 0);
            const performanceScore = parseFloat(document.getElementById('performance_score').value || 0);
            const designScore = parseFloat(document.getElementById('design_score').value || 0);
            const batteryScore = parseFloat(document.getElementById('battery_score').value || 0);

            radarChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Kamera', 'Performa', 'Desain', 'Baterai'],
                    datasets: [{
                        label: 'Skor Smartphone',
                        data: [cameraScore, performanceScore, designScore, batteryScore],
                        backgroundColor: 'rgba(109, 40, 217, 0.2)',
                        borderColor: '#6d28d9',
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#10b981'
                    }]
                },
                options: {
                    scales: {
                        r: {
                            angleLines: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            pointLabels: {
                                color: '#f3f4f6'
                            },
                            ticks: {
                                display: false,
                                max: 10,
                                min: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    animation: {
                        duration: 500
                    }
                }
            });
        }

        function updateRadarChart() {
            if (!radarChart) return;

            const cameraScore = parseFloat(document.getElementById('camera_score').value || 0);
            const performanceScore = parseFloat(document.getElementById('performance_score').value || 0);
            const designScore = parseFloat(document.getElementById('design_score').value || 0);
            const batteryScore = parseFloat(document.getElementById('battery_score').value || 0);

            radarChart.data.datasets[0].data = [cameraScore, performanceScore, designScore, batteryScore];
            radarChart.update();
        }

        // Initialize everything when document is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize progress bars
            updateScoreProgress('camera_score', document.getElementById('camera_score').value);
            updateScoreProgress('performance_score', document.getElementById('performance_score').value);
            updateScoreProgress('design_score', document.getElementById('design_score').value);
            updateScoreProgress('battery_score', document.getElementById('battery_score').value);

            // Initialize Radar Chart
            initRadarChart();
        });
    </script>
@endsection
