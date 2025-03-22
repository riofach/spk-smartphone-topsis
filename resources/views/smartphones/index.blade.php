@extends('layouts.app')

@section('title', 'Daftar Smartphone')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8" data-aos="fade-right">
            <h2 class="fw-bold text-gradient">Daftar Smartphone</h2>
            <p class="text-white">Semua smartphone yang tersedia dalam database sistem.</p>
        </div>
        <div class="col-md-4 text-end" data-aos="fade-left">
            <a href="{{ route('smartphones.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Smartphone
            </a>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card shadow mb-4" data-aos="fade-up">
        <div class="card-body p-4">
            <form action="{{ route('smartphones.index') }}" method="GET" id="searchFilterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="search-container position-relative">
                            <label for="search" class="form-label"><i class="fas fa-search me-2"></i>Cari
                                Smartphone</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari nama, prosesor, atau deskripsi" value="{{ request('search') }}"
                                autocomplete="off">
                            <div class="search-results-container d-none">
                                <div id="searchResults" class="p-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="sort" class="form-label"><i class="fas fa-sort me-2"></i>Urutkan</label>
                        <select class="form-select" id="sort" name="sort" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>
                                Harga: Rendah ke Tinggi</option>
                            <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>
                                Harga: Tinggi ke Rendah</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block"><i class="fas fa-filter me-2"></i>Filter</label>
                        <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="collapse"
                            data-bs-target="#filterCollapse" aria-expanded="false">
                            <i class="fas fa-sliders-h me-2"></i>Tampilkan Filter
                            @if (request()->anyFilled(['min_price', 'max_price', 'ram', 'storage', 'release_year']))
                                <span class="badge bg-primary ms-2">Aktif</span>
                            @endif
                        </button>
                    </div>
                </div>

                <div class="collapse mt-3 {{ request()->anyFilled(['min_price', 'max_price', 'ram', 'storage', 'release_year']) ? 'show' : '' }}"
                    id="filterCollapse">
                    <div class="card card-body" style="background-color: var(--card-bg);">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Rentang Harga</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="min_price" name="min_price"
                                            placeholder="Min" value="{{ request('min_price') }}">
                                    </div>
                                    <span>-</span>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="max_price" name="max_price"
                                            placeholder="Max" value="{{ request('max_price') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="ram" class="form-label">RAM (GB)</label>
                                <select class="form-select" id="ram" name="ram">
                                    <option value="">Semua</option>
                                    @foreach ($ramOptions as $ram)
                                        <option value="{{ $ram }}" {{ request('ram') == $ram ? 'selected' : '' }}>
                                            {{ $ram }} GB</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="storage" class="form-label">Storage (GB)</label>
                                <select class="form-select" id="storage" name="storage">
                                    <option value="">Semua</option>
                                    @foreach ($storageOptions as $storage)
                                        <option value="{{ $storage }}"
                                            {{ request('storage') == $storage ? 'selected' : '' }}>{{ $storage }} GB
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="release_year" class="form-label">Tahun Rilis</label>
                                <select class="form-select" id="release_year" name="release_year">
                                    <option value="">Semua</option>
                                    @foreach ($releaseYearOptions as $year)
                                        <option value="{{ $year }}"
                                            {{ request('release_year') == $year ? 'selected' : '' }}>{{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3 gap-2">
                            <a href="{{ route('smartphones.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Reset
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($smartphones->isEmpty())
        <div class="alert alert-info" data-aos="fade-up">
            <p><i class="fas fa-info-circle me-2"></i>Tidak ada smartphone yang ditemukan. Coba ubah filter pencarian Anda
                atau tambahkan data smartphone baru.</p>
        </div>
    @else
        <div class="card shadow" data-aos="fade-up">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">#</th>
                                <th width="80">Gambar</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th class="text-center" width="70">
                                    <i class="fas fa-camera" data-bs-toggle="tooltip" title="Kamera"></i>
                                </th>
                                <th class="text-center" width="70">
                                    <i class="fas fa-microchip" data-bs-toggle="tooltip" title="Performa"></i>
                                </th>
                                <th class="text-center" width="70">
                                    <i class="fas fa-palette" data-bs-toggle="tooltip" title="Desain"></i>
                                </th>
                                <th class="text-center" width="70">
                                    <i class="fas fa-battery-full" data-bs-toggle="tooltip" title="Baterai"></i>
                                </th>
                                <th class="text-center" width="70">
                                    <i class="fas fa-calendar" data-bs-toggle="tooltip" title="Tahun Rilis"></i>
                                </th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($smartphones as $index => $smartphone)
                                <tr class="align-middle" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                                    <td class="text-center">
                                        {{ ($smartphones->currentPage() - 1) * $smartphones->perPage() + $index + 1 }}</td>
                                    <td>
                                        <img src="{{ $smartphone->image_url }}" alt="{{ $smartphone->name }}"
                                            class="img-thumbnail" style="width: 50px; height: 50px; object-fit: contain;">
                                    </td>
                                    <td class="fw-medium">{{ $smartphone->name }}</td>
                                    <td>Rp {{ number_format($smartphone->price, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ getScoreColor($smartphone->camera_score) }}">
                                            {{ number_format($smartphone->camera_score, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ getScoreColor($smartphone->performance_score) }}">
                                            {{ number_format($smartphone->performance_score, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ getScoreColor($smartphone->design_score) }}">
                                            {{ number_format($smartphone->design_score, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ getScoreColor($smartphone->battery_score) }}">
                                            {{ number_format($smartphone->battery_score, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ $smartphone->release_year }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('smartphones.edit', $smartphone) }}"
                                                class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                title="Edit Smartphone">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('smartphones.destroy', $smartphone) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-bs-toggle="tooltip" title="Hapus Smartphone">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <button class="btn btn-sm btn-primary detail-btn" data-bs-toggle="modal"
                                                data-bs-target="#detailModal"
                                                data-smartphone="{{ json_encode($smartphone) }}" title="Lihat Detail">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container mt-4" data-aos="fade-up">
            {{ $smartphones->links('vendor.pagination.custom') }}
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-black">
                    <p>Apakah Anda yakin ingin menghapus smartphone ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Image Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
        aria-hidden="true">
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background-color: var(--modal-bg); color: var(--text-light);">
                <div class="modal-header border-bottom border-dark">
                    <h5 class="modal-title text-gradient" id="detailModalLabel">Detail Smartphone</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 text-center mb-4">
                            <img id="detailImage" src="" alt="Detail Image"
                                class="img-fluid mb-3 rounded shadow" style="max-width: 200px;">
                            <h4 id="detailName" class="text-gradient mb-3"></h4>
                            <span id="detailBrand" class="badge bg-primary mb-2 d-block mx-auto"
                                style="max-width: fit-content;"></span>
                            <h5 id="detailPrice" class="text-warning"></h5>
                        </div>
                        <div class="col-md-7">
                            <div class="card mb-3" style="background-color: rgba(31, 41, 55, 0.5);">
                                <div class="card-header bg-dark">
                                    <h5 class="mb-0 text-gradient">Visualisasi Skor</h5>
                                </div>
                                <div class="card-body text-center p-3">
                                    <canvas id="radarChart" width="250" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card mb-3" style="background-color: rgba(31, 41, 55, 0.5);">
                                <div class="card-header bg-dark">
                                    <h6 class="mb-0 text-light"><i class="fas fa-info-circle me-2"></i>Informasi Dasar
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <p><i class="fas fa-calendar me-2 text-info"></i><strong>Tahun:</strong> <span
                                                    id="detailYear"></span></p>
                                            <p><i class="fas fa-memory me-2 text-info"></i><strong>RAM:</strong> <span
                                                    id="detailRam"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p><i class="fas fa-hdd me-2 text-info"></i><strong>Storage:</strong> <span
                                                    id="detailStorage"></span></p>
                                            <p><i class="fas fa-microchip me-2 text-info"></i><strong>Prosesor:</strong>
                                                <span id="detailProcessor"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3" style="background-color: rgba(31, 41, 55, 0.5);">
                                <div class="card-header bg-dark">
                                    <h6 class="mb-0 text-light"><i class="fas fa-chart-bar me-2"></i>Spesifikasi Teknis
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <p><i
                                                    class="fas fa-battery-full me-2 text-success"></i><strong>Baterai:</strong>
                                                <span id="detailBattery"></span>
                                            </p>
                                            <p><i class="fas fa-mobile-alt me-2 text-success"></i><strong>Layar:</strong>
                                                <span id="detailScreen"></span>
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <p><i class="fas fa-camera me-2 text-success"></i><strong>Kamera:</strong>
                                                <span id="detailCamera"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3" style="background-color: rgba(31, 41, 55, 0.5);">
                        <div class="card-header bg-dark">
                            <h6 class="mb-0 text-light"><i class="fas fa-star me-2"></i>Skor Kriterianya</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span><i class="fas fa-camera me-2"></i>Kamera</span>
                                        <span id="detailCameraScore"></span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div id="detailCameraProgress" class="progress-bar" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span><i class="fas fa-microchip me-2"></i>Performa</span>
                                        <span id="detailPerformanceScore"></span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div id="detailPerformanceProgress" class="progress-bar" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span><i class="fas fa-palette me-2"></i>Desain</span>
                                        <span id="detailDesignScore"></span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div id="detailDesignProgress" class="progress-bar" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span><i class="fas fa-battery-full me-2"></i>Baterai</span>
                                        <span id="detailBatteryScore"></span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div id="detailBatteryProgress" class="progress-bar" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi tooltip
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Konfirmasi delete dengan modal
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let currentForm = null;

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentForm = this.closest('form');
                    deleteModal.show();
                });
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (currentForm) {
                    currentForm.submit();
                }
                deleteModal.hide();
            });

            // Preview image modal
            const previewButtons = document.querySelectorAll('.preview-btn');
            const imagePreviewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            const previewImage = document.getElementById('previewImage');
            const previewTitle = document.getElementById('previewTitle');

            previewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const imgSrc = this.getAttribute('data-img');
                    const imgName = this.getAttribute('data-name');

                    previewImage.src = imgSrc;
                    previewTitle.textContent = imgName;
                    imagePreviewModal.show();
                });
            });

            // Detail modal dan Radar Chart
            let radarChart;

            const detailButtons = document.querySelectorAll('.detail-btn');
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const smartphone = JSON.parse(this.getAttribute('data-smartphone'));
                    console.log('Smartphone data:', smartphone); // Debugging

                    // Set data detail smartphone
                    document.getElementById('detailImage').src = smartphone.image_url;
                    document.getElementById('detailName').textContent = smartphone.name;
                    document.getElementById('detailBrand').textContent = smartphone.brand ||
                        'Not specified';
                    document.getElementById('detailPrice').textContent =
                        `Rp ${new Intl.NumberFormat('id-ID').format(smartphone.price)}`;
                    document.getElementById('detailYear').textContent = smartphone.release_year ||
                        'N/A';
                    document.getElementById('detailRam').textContent = smartphone.ram ?
                        `${smartphone.ram} GB` : 'N/A';
                    document.getElementById('detailStorage').textContent = smartphone.storage ?
                        `${smartphone.storage} GB` : 'N/A';
                    document.getElementById('detailBattery').textContent = smartphone.battery ?
                        `${smartphone.battery} mAh` : 'N/A';
                    document.getElementById('detailCamera').textContent = smartphone.camera ?
                        `${smartphone.camera} MP` : 'N/A';
                    document.getElementById('detailScreen').textContent = smartphone.screen_size ?
                        `${smartphone.screen_size} inch` : 'N/A';
                    document.getElementById('detailProcessor').textContent = smartphone.processor ||
                        'N/A';

                    // Pastikan nilai skor ada
                    const cameraScore = parseFloat(smartphone.camera_score || 0);
                    const performanceScore = parseFloat(smartphone.performance_score || 0);
                    const designScore = parseFloat(smartphone.design_score || 0);
                    const batteryScore = parseFloat(smartphone.battery_score || 0);

                    // Set badge untuk skor
                    document.getElementById('detailCameraScore').innerHTML =
                        `<span class="badge bg-${getScoreColor(cameraScore)}">${cameraScore.toFixed(1)}</span>`;
                    document.getElementById('detailPerformanceScore').innerHTML =
                        `<span class="badge bg-${getScoreColor(performanceScore)}">${performanceScore.toFixed(1)}</span>`;
                    document.getElementById('detailDesignScore').innerHTML =
                        `<span class="badge bg-${getScoreColor(designScore)}">${designScore.toFixed(1)}</span>`;
                    document.getElementById('detailBatteryScore').innerHTML =
                        `<span class="badge bg-${getScoreColor(batteryScore)}">${batteryScore.toFixed(1)}</span>`;

                    // Set progress bar
                    updateProgressBar('detailCameraProgress', cameraScore);
                    updateProgressBar('detailPerformanceProgress', performanceScore);
                    updateProgressBar('detailDesignProgress', designScore);
                    updateProgressBar('detailBatteryProgress', batteryScore);

                    // Inisialisasi atau update Radar Chart
                    if (radarChart) {
                        radarChart.destroy();
                    }

                    initRadarChart(smartphone);
                });
            });

            // Inisialisasi Radar Chart
            function initRadarChart(smartphone) {
                const ctx = document.getElementById('radarChart').getContext('2d');

                // Pastikan nilai skor ada
                const cameraScore = parseFloat(smartphone.camera_score || 0);
                const performanceScore = parseFloat(smartphone.performance_score || 0);
                const designScore = parseFloat(smartphone.design_score || 0);
                const batteryScore = parseFloat(smartphone.battery_score || 0);

                radarChart = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: ['Kamera', 'Performa', 'Desain', 'Baterai'],
                        datasets: [{
                            label: 'Skor Smartphone',
                            data: [
                                cameraScore,
                                performanceScore,
                                designScore,
                                batteryScore
                            ],
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
                                    color: '#f3f4f6',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
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
                        elements: {
                            line: {
                                tension: 0.1
                            }
                        }
                    }
                });
            }

            // Update progress bar
            function updateProgressBar(id, value) {
                const progressBar = document.getElementById(id);
                const percentage = (value / 10) * 100;
                progressBar.style.width = `${percentage}%`;

                // Set warna progress bar berdasarkan nilai
                if (value >= 9) {
                    progressBar.className = 'progress-bar bg-success';
                } else if (value >= 7) {
                    progressBar.className = 'progress-bar bg-primary';
                } else if (value >= 5) {
                    progressBar.className = 'progress-bar bg-info';
                } else if (value >= 3) {
                    progressBar.className = 'progress-bar bg-warning';
                } else {
                    progressBar.className = 'progress-bar bg-danger';
                }
            }

            // Fungsi untuk mendapatkan warna berdasarkan nilai skor
            function getScoreColor(score) {
                if (score >= 9) {
                    return 'success';
                }
                if (score >= 7) {
                    return 'primary';
                }
                if (score >= 5) {
                    return 'info';
                }
                if (score >= 3) {
                    return 'warning';
                }
                return 'danger';
            }

            // Fungsi Go to page
            window.goToPage = function() {
                const input = document.getElementById('goToPage');
                const page = parseInt(input.value);
                const maxPage = parseInt(input.max);

                if (page && page > 0 && page <= maxPage) {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('page', page);
                    window.location.href = currentUrl.toString();
                } else {
                    alert('Halaman tidak valid!');
                }
            }

            // Handle Enter key pada input Go to page
            document.getElementById('goToPage')?.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    goToPage();
                }
            });

            // Autocomplete search with real-time table update
            const searchInput = document.getElementById('search');
            const tableBody = document.querySelector('table tbody');
            const noResultsAlert = document.querySelector('.alert-info');
            const tableContainer = document.querySelector('.card.shadow');
            const paginationContainer = document.querySelector('.pagination-container');
            let debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                debounceTimer = setTimeout(() => {
                    // Jika query kosong, kembalikan ke tampilan awal
                    if (query === '') {
                        window.location.href = '{{ route('smartphones.index') }}';
                        return;
                    }

                    // Show loading indicator
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Mencari smartphone...</p>
                            </td>
                        </tr>
                    `;

                    // Fetch filtered results dengan header Accept: application/json
                    fetch(`{{ route('smartphones.index') }}?query=${encodeURIComponent(query)}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Data from server:', data); // Debugging

                            if (data.error) {
                                throw new Error(data.message ||
                                    'Terjadi kesalahan pada server');
                            }

                            if (!data.smartphones || data.smartphones.length === 0) {
                                // No results
                                if (tableContainer) tableContainer.classList.add('d-none');
                                if (paginationContainer) paginationContainer.classList.add(
                                    'd-none');

                                // Show no results message
                                if (!noResultsAlert) {
                                    const alertDiv = document.createElement('div');
                                    alertDiv.className = 'alert alert-info';
                                    alertDiv.setAttribute('data-aos', 'fade-up');
                                    alertDiv.innerHTML =
                                        `<p><i class="fas fa-info-circle me-2"></i>Tidak ada smartphone yang ditemukan. Coba ubah filter pencarian Anda.</p>`;
                                    tableContainer.parentNode.insertBefore(alertDiv,
                                        tableContainer);
                                } else {
                                    noResultsAlert.classList.remove('d-none');
                                }
                            } else {
                                // Show results
                                if (noResultsAlert) noResultsAlert.classList.add('d-none');
                                if (tableContainer) tableContainer.classList.remove('d-none');
                                if (paginationContainer) paginationContainer.classList.add(
                                    'd-none'); // Hide pagination for real-time results

                                // Update table with results
                                tableBody.innerHTML = '';
                                data.smartphones.forEach((smartphone, index) => {
                                    const row = document.createElement('tr');
                                    row.className = 'align-middle';
                                    row.setAttribute('data-aos', 'fade-up');
                                    row.setAttribute('data-aos-delay', `${index * 50}`);

                                    // Format smartphone data for display
                                    row.innerHTML = `
                                        <td class="text-center">${index + 1}</td>
                                        <td>
                                            <img src="${smartphone.image_url}" alt="${smartphone.name}"
                                                class="img-thumbnail" style="width: 50px; height: 50px; object-fit: contain;">
                                        </td>
                                        <td class="fw-medium">${smartphone.name}</td>
                                        <td>Rp ${new Intl.NumberFormat('id-ID').format(smartphone.price)}</td>
                                        <td class="text-center">
                                            <span class="badge bg-${getScoreColor(smartphone.camera_score)}">
                                                ${parseFloat(smartphone.camera_score).toFixed(1)}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-${getScoreColor(smartphone.performance_score)}">
                                                ${parseFloat(smartphone.performance_score).toFixed(1)}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-${getScoreColor(smartphone.design_score)}">
                                                ${parseFloat(smartphone.design_score).toFixed(1)}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-${getScoreColor(smartphone.battery_score)}">
                                                ${parseFloat(smartphone.battery_score).toFixed(1)}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            ${smartphone.release_year}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="${smartphone.edit_url}" 
                                                   class="btn btn-sm btn-warning" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="${smartphone.delete_url}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger delete-btn" 
                                                            data-bs-toggle="tooltip" 
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <button class="btn btn-sm btn-primary detail-btn" 
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailModal"
                                                        data-smartphone='${JSON.stringify(smartphone)}' 
                                                        title="Lihat Detail">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                
                                            </div>
                                        </td>
                                    `;

                                    tableBody.appendChild(row);
                                });

                                // Re-initialize tooltips and event listeners for the new elements
                                const tooltipTriggerList = document.querySelectorAll(
                                    '[data-bs-toggle="tooltip"]');
                                [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap
                                    .Tooltip(tooltipTriggerEl));

                                // Re-initialize detail buttons and delete confirmation
                                initializeDetailButtons();
                                initializeDeleteButtons();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-danger">
                                        <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                        <p>Terjadi kesalahan saat memuat data. Silakan coba lagi.</p>
                                        <small class="text-muted">${error.message}</small>
                                        <div class="mt-3">
                                            <button onclick="window.location.reload()" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-sync-alt me-2"></i>Muat Ulang
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                }, 300);
            });

            // Initialize delete confirmation
            function initializeDeleteButtons() {
                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Data smartphone ini akan dihapus secara permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });
            }

            // Helper function to initialize detail buttons
            function initializeDetailButtons() {
                const detailButtons = document.querySelectorAll('.detail-btn');

                detailButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const smartphone = JSON.parse(this.getAttribute('data-smartphone'));

                        // Set data detail smartphone
                        document.getElementById('detailImage').src = smartphone.image_url;
                        document.getElementById('detailName').textContent = smartphone.name;
                        document.getElementById('detailBrand').textContent = smartphone.brand ||
                            'Not specified';
                        document.getElementById('detailPrice').textContent =
                            `Rp ${new Intl.NumberFormat('id-ID').format(smartphone.price)}`;
                        document.getElementById('detailYear').textContent = smartphone
                            .release_year || 'N/A';
                        document.getElementById('detailRam').textContent = smartphone.ram ?
                            `${smartphone.ram} GB` : 'N/A';
                        document.getElementById('detailStorage').textContent = smartphone.storage ?
                            `${smartphone.storage} GB` : 'N/A';
                        document.getElementById('detailBattery').textContent = smartphone.battery ?
                            `${smartphone.battery} mAh` : 'N/A';
                        document.getElementById('detailCamera').textContent = smartphone.camera ?
                            `${smartphone.camera} MP` : 'N/A';
                        document.getElementById('detailScreen').textContent = smartphone
                            .screen_size ? `${smartphone.screen_size} inch` : 'N/A';
                        document.getElementById('detailProcessor').textContent = smartphone
                            .processor || 'N/A';

                        // Pastikan nilai skor ada
                        const cameraScore = parseFloat(smartphone.camera_score || 0);
                        const performanceScore = parseFloat(smartphone.performance_score || 0);
                        const designScore = parseFloat(smartphone.design_score || 0);
                        const batteryScore = parseFloat(smartphone.battery_score || 0);

                        // Set badge untuk skor
                        document.getElementById('detailCameraScore').innerHTML =
                            `<span class="badge bg-${getScoreColor(cameraScore)}">${cameraScore.toFixed(1)}</span>`;
                        document.getElementById('detailPerformanceScore').innerHTML =
                            `<span class="badge bg-${getScoreColor(performanceScore)}">${performanceScore.toFixed(1)}</span>`;
                        document.getElementById('detailDesignScore').innerHTML =
                            `<span class="badge bg-${getScoreColor(designScore)}">${designScore.toFixed(1)}</span>`;
                        document.getElementById('detailBatteryScore').innerHTML =
                            `<span class="badge bg-${getScoreColor(batteryScore)}">${batteryScore.toFixed(1)}</span>`;

                        // Set progress bar
                        updateProgressBar('detailCameraProgress', cameraScore);
                        updateProgressBar('detailPerformanceProgress', performanceScore);
                        updateProgressBar('detailDesignProgress', designScore);
                        updateProgressBar('detailBatteryProgress', batteryScore);

                        // Inisialisasi atau update Radar Chart
                        if (radarChart) {
                            radarChart.destroy();
                        }

                        initRadarChart(smartphone);
                    });
                });
            }

            // Form filters auto-submit
            const autoSubmitElements = document.querySelectorAll('#ram, #storage, #release_year');
            autoSubmitElements.forEach(element => {
                element.addEventListener('change', function() {
                    document.getElementById('searchFilterForm').submit();
                });
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        /* Custom pagination style */
        .pagination-arrow {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: rgba(30, 41, 59, 0.8);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination-arrow:hover:not(.disabled) {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            transform: translateY(-2px);
        }

        .pagination-arrow.disabled {
            background: rgba(30, 41, 59, 0.4);
            color: #64748b;
            cursor: not-allowed;
        }

        .pagination-number {
            min-width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(30, 41, 59, 0.8);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .pagination-number:hover:not(.active) {
            background: #2d3748;
            color: white;
            transform: translateY(-2px);
        }

        .pagination-number.active {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white;
            font-weight: 600;
        }

        .pagination-ellipsis {
            color: #e2e8f0;
            padding: 0 4px;
        }

        .go-to-page {
            background: rgba(30, 41, 59, 0.8);
            padding: 4px 12px;
            border-radius: 20px;
        }

        .go-to-page input {
            width: 60px;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid #2d3748;
            color: #e2e8f0;
            text-align: center;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .go-to-page button {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            border: none;
            width: 24px;
            height: 24px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .go-to-page button:hover {
            transform: translateX(2px);
        }

        /* Search and Filter styles */
        .search-container {
            position: relative;
        }

        .search-results-container {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            margin-top: 4px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            z-index: 9999 !important;
            max-height: 300px;
            overflow-y: auto;
            background-color: var(--modal-bg, #1a202c);
        }

        .search-result-item {
            border-radius: 4px;
        }

        .search-result-item:last-child {
            border-bottom: none !important;
        }

        .form-control,
        .form-select {
            background-color: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-light);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--input-bg-focus);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(109, 40, 217, 0.25);
            color: var(--text-light);
        }

        .form-label {
            color: var(--text-light);
            font-weight: 500;
        }

        .input-group-text {
            background-color: var(--input-group-bg);
            border-color: var(--border-color);
            color: var(--text-light);
        }
    </style>
@endsection

@php
    function getScoreColor($score)
    {
        if ($score >= 9) {
            return 'success';
        }
        if ($score >= 7) {
            return 'primary';
        }
        if ($score >= 5) {
            return 'info';
        }
        if ($score >= 3) {
            return 'warning';
        }
        return 'danger';
    }
@endphp
