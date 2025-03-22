@extends('layouts.app')

@section('title', 'Edit Smartphone')

@section('content')
    <div class="row">
        <div class="col-md-12" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold text-gradient">Edit Smartphone</h2>
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
                    <form action="{{ route('smartphones.update', $smartphone) }}" method="POST"
                        enctype="multipart/form-data" id="smartphoneForm">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6" data-aos="fade-right" data-aos-delay="200">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-mobile-alt me-2"></i>Nama Smartphone <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $smartphone->name) }}" required>
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
                                            id="price" name="price" value="{{ old('price', $smartphone->price) }}"
                                            min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">
                                        <i class="fas fa-image me-2"></i>Gambar Smartphone (PNG, Maks. 1MB)
                                    </label>
                                    <div class="image-container mb-2 text-center position-relative overflow-hidden rounded">
                                        @if ($smartphone->image_url)
                                            <img src="{{ $smartphone->image_url }}" alt="{{ $smartphone->name }}"
                                                class="img-fluid" style="max-height: 200px;" id="currentImage">
                                            <div class="image-overlay">
                                                <span class="badge bg-primary">Current Image</span>
                                            </div>
                                        @else
                                            <div class="no-image-container p-4 rounded text-center">
                                                <i class="fas fa-image fa-4x mb-3 text-muted"></i>
                                                <p class="text-muted">Tidak ada gambar</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <div class="mt-2 text-center d-none" id="imagePreviewContainer">
                                        <div class="position-relative overflow-hidden rounded">
                                            <img id="imagePreview" src="#" alt="Preview" class="img-fluid"
                                                style="max-height: 200px;">
                                            <div class="image-overlay">
                                                <span class="badge bg-success">New Image</span>
                                            </div>
                                        </div>
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-white">Format yang diizinkan: JPG, PNG, GIF dengan ukuran maksimal
                                        2MB. Biarkan
                                        kosong jika tidak ingin mengubah gambar.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left me-2"></i>Deskripsi
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="4">{{ old('description', $smartphone->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="release_year" class="form-label">Tahun Rilis</label>
                                    <select class="form-select @error('release_year') is-invalid @enderror"
                                        id="release_year" name="release_year" required>
                                        <option value="" disabled>-- Pilih Tahun Rilis --</option>
                                        @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                            <option value="{{ $year }}"
                                                {{ old('release_year', $smartphone->release_year) == $year ? 'selected' : '' }}>
                                                {{ $year }}</option>
                                        @endfor
                                    </select>
                                    <div class="form-text text-warning">
                                        <i class="fas fa-info-circle"></i> Hanya smartphone dengan tahun rilis maksimal 2
                                        tahun terakhir yang dapat ditambahkan.
                                    </div>
                                    @error('release_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-dark">
                                        <h5 class="mb-0 text-gradient">Spesifikasi Teknis</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="ram" class="form-label">
                                                        <i class="fas fa-memory me-2"></i>RAM (GB) <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                        class="form-control @error('ram') is-invalid @enderror"
                                                        id="ram" name="ram"
                                                        value="{{ old('ram', $smartphone->ram) }}" min="1"
                                                        required>
                                                    @error('ram')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="storage" class="form-label">
                                                        <i class="fas fa-hdd me-2"></i>Storage (GB) <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                        class="form-control @error('storage') is-invalid @enderror"
                                                        id="storage" name="storage"
                                                        value="{{ old('storage', $smartphone->storage) }}" min="8"
                                                        required>
                                                    @error('storage')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="processor" class="form-label">
                                                <i class="fas fa-microchip me-2"></i>Processor <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('processor') is-invalid @enderror"
                                                id="processor" name="processor"
                                                value="{{ old('processor', $smartphone->processor) }}" required>
                                            @error('processor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="battery" class="form-label">
                                                        <i class="fas fa-battery-full me-2"></i>Baterai (mAh) <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                        class="form-control @error('battery') is-invalid @enderror"
                                                        id="battery" name="battery"
                                                        value="{{ old('battery', $smartphone->battery) }}" min="1000"
                                                        required>
                                                    @error('battery')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="camera" class="form-label">
                                                        <i class="fas fa-camera me-2"></i>Kamera (MP) <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                        class="form-control @error('camera') is-invalid @enderror"
                                                        id="camera" name="camera"
                                                        value="{{ old('camera', $smartphone->camera) }}" min="5"
                                                        required>
                                                    @error('camera')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="screen_size" class="form-label">
                                                        <i class="fas fa-mobile-alt me-2"></i>Ukuran Layar (inch) <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" step="0.1"
                                                        class="form-control @error('screen_size') is-invalid @enderror"
                                                        id="screen_size" name="screen_size"
                                                        value="{{ old('screen_size', $smartphone->screen_size) }}"
                                                        min="3" max="10" required>
                                                    @error('screen_size')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                                <i class="fas fa-camera me-2"></i>Skor Kamera (0-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="camera_score_range"
                                                    value="{{ old('camera_score', $smartphone->camera_score) }}"
                                                    oninput="updateScore('camera_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('camera_score') is-invalid @enderror"
                                                    id="camera_score" name="camera_score"
                                                    value="{{ old('camera_score', $smartphone->camera_score) }}"
                                                    min="0" max="10" step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('camera_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="camera_score_progress" class="progress-bar" role="progressbar"
                                                    style="width: {{ ($smartphone->camera_score / 10) * 100 }}%;"
                                                    aria-valuenow="{{ $smartphone->camera_score }}" aria-valuemin="0"
                                                    aria-valuemax="10"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">0</small>
                                                <small class="text-muted"
                                                    id="camera_score_label">{{ $smartphone->camera_score }}</small>
                                                <small class="text-muted">10</small>
                                            </div>
                                            @error('camera_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="performance_score" class="form-label">
                                                <i class="fas fa-microchip me-2"></i>Skor Performa (0-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="performance_score_range"
                                                    value="{{ old('performance_score', $smartphone->performance_score) }}"
                                                    oninput="updateScore('performance_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('performance_score') is-invalid @enderror"
                                                    id="performance_score" name="performance_score"
                                                    value="{{ old('performance_score', $smartphone->performance_score) }}"
                                                    min="0" max="10" step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('performance_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="performance_score_progress" class="progress-bar"
                                                    role="progressbar"
                                                    style="width: {{ ($smartphone->performance_score / 10) * 100 }}%;"
                                                    aria-valuenow="{{ $smartphone->performance_score }}"
                                                    aria-valuemin="0" aria-valuemax="10"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">0</small>
                                                <small class="text-muted"
                                                    id="performance_score_label">{{ $smartphone->performance_score }}</small>
                                                <small class="text-muted">10</small>
                                            </div>
                                            @error('performance_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="design_score" class="form-label">
                                                <i class="fas fa-palette me-2"></i>Skor Desain (0-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="design_score_range"
                                                    value="{{ old('design_score', $smartphone->design_score) }}"
                                                    oninput="updateScore('design_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('design_score') is-invalid @enderror"
                                                    id="design_score" name="design_score"
                                                    value="{{ old('design_score', $smartphone->design_score) }}"
                                                    min="0" max="10" step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('design_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="design_score_progress" class="progress-bar" role="progressbar"
                                                    style="width: {{ ($smartphone->design_score / 10) * 100 }}%;"
                                                    aria-valuenow="{{ $smartphone->design_score }}" aria-valuemin="0"
                                                    aria-valuemax="10"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">0</small>
                                                <small class="text-muted"
                                                    id="design_score_label">{{ $smartphone->design_score }}</small>
                                                <small class="text-muted">10</small>
                                            </div>
                                            @error('design_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="battery_score" class="form-label">
                                                <i class="fas fa-battery-full me-2"></i>Skor Baterai (0-10) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="range" class="form-range flex-grow-1" min="0"
                                                    max="10" step="0.1" id="battery_score_range"
                                                    value="{{ old('battery_score', $smartphone->battery_score) }}"
                                                    oninput="updateScore('battery_score', this.value)">
                                                <input type="number"
                                                    class="form-control @error('battery_score') is-invalid @enderror"
                                                    id="battery_score" name="battery_score"
                                                    value="{{ old('battery_score', $smartphone->battery_score) }}"
                                                    min="0" max="10" step="0.1" style="width: 80px;"
                                                    oninput="updateScoreRange('battery_score', this.value)" required>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div id="battery_score_progress" class="progress-bar" role="progressbar"
                                                    style="width: {{ ($smartphone->battery_score / 10) * 100 }}%;"
                                                    aria-valuenow="{{ $smartphone->battery_score }}" aria-valuemin="0"
                                                    aria-valuemax="10"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">0</small>
                                                <small class="text-muted"
                                                    id="battery_score_label">{{ $smartphone->battery_score }}</small>
                                                <small class="text-muted">10</small>
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
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('smartphones.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .image-container:hover {
            transform: scale(1.02);
        }

        .image-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            transition: all 0.3s ease;
            opacity: 0.8;
        }

        .image-container:hover .image-overlay {
            opacity: 1;
        }

        .no-image-container {
            background-color: var(--dark-lighter);
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .radar-chart-card {
            transition: all 0.3s ease;
        }

        .radar-chart-card:hover {
            transform: translateY(-5px) scale(1.01);
        }

        /* Customize sliders for each criteria */
        #camera_score_range::-webkit-slider-thumb {
            background: linear-gradient(135deg, #6d28d9, #9333ea);
        }

        #performance_score_range::-webkit-slider-thumb {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        #design_score_range::-webkit-slider-thumb {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        #battery_score_range::-webkit-slider-thumb {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        /* Progress bar colors */
        #camera_score_progress {
            background: linear-gradient(to right, #6d28d9, #9333ea);
        }

        #performance_score_progress {
            background: linear-gradient(to right, #10b981, #059669);
        }

        #design_score_progress {
            background: linear-gradient(to right, #f59e0b, #d97706);
        }

        #battery_score_progress {
            background: linear-gradient(to right, #3b82f6, #2563eb);
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Preview image when uploaded
        function previewImage(input) {
            const container = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            const currentImage = document.getElementById('currentImage');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('d-none');

                    // Add fade effect
                    if (currentImage) {
                        currentImage.style.opacity = '0.5';
                    }
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = "#";
                container.classList.add('d-none');

                // Reset current image
                if (currentImage) {
                    currentImage.style.opacity = '1';
                }
            }
        }

        // Update score when range is changed
        function updateScore(fieldId, value) {
            document.getElementById(fieldId).value = value;
            updateScoreProgress(fieldId, value);
            updateScoreLabel(fieldId, value);
            updateRadarChart();
        }

        // Update range when score is changed
        function updateScoreRange(fieldId, value) {
            document.getElementById(fieldId + '_range').value = value;
            updateScoreProgress(fieldId, value);
            updateScoreLabel(fieldId, value);
            updateRadarChart();
        }

        // Update progress bar
        function updateScoreProgress(fieldId, value) {
            const progressBar = document.getElementById(fieldId + '_progress');
            const percentage = (value / 10) * 100;
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', value);
        }

        // Update score label
        function updateScoreLabel(fieldId, value) {
            const label = document.getElementById(fieldId + '_label');
            label.textContent = value;

            // Change color based on score
            if (value >= 8) {
                label.className = "text-gradient small fw-bold";
            } else if (value >= 6) {
                label.className = "text-success small";
            } else if (value >= 4) {
                label.className = "text-warning small";
            } else {
                label.className = "text-danger small";
            }
        }

        // Radar Chart
        let radarChart;

        function initRadarChart() {
            const ctx = document.getElementById('radarChart').getContext('2d');

            const cameraScore = parseFloat(document.getElementById('camera_score').value || 0);
            const performanceScore = parseFloat(document.getElementById('performance_score').value || 0);
            const designScore = parseFloat(document.getElementById('design_score').value || 0);
            const batteryScore = parseFloat(document.getElementById('battery_score').value || 0);

            // Color gradients for radar chart
            const gradientCamera = ctx.createLinearGradient(0, 0, 0, 150);
            gradientCamera.addColorStop(0, 'rgba(109, 40, 217, 0.7)');
            gradientCamera.addColorStop(1, 'rgba(147, 51, 234, 0.3)');

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
                        pointHoverBorderColor: '#10b981',
                        pointRadius: 5,
                        pointHoverRadius: 7
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
                                color: '#f3f4f6',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                display: false,
                                backdropColor: 'transparent',
                                max: 10,
                                min: 0,
                                stepSize: 2,
                                color: 'rgba(255, 255, 255, 0.5)',
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 10,
                            borderColor: '#6d28d9',
                            borderWidth: 1
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
            // Initialize score labels
            updateScoreLabel('camera_score', document.getElementById('camera_score').value);
            updateScoreLabel('performance_score', document.getElementById('performance_score').value);
            updateScoreLabel('design_score', document.getElementById('design_score').value);
            updateScoreLabel('battery_score', document.getElementById('battery_score').value);

            // Initialize Radar Chart with animation
            setTimeout(() => {
                initRadarChart();
            }, 500);

            // Add smooth transition to form inputs
            const formInputs = document.querySelectorAll('input, textarea');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.1)';
                });

                input.addEventListener('blur', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
@endsection
