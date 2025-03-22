<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background-color: var(--modal-bg, #1a202c); color: var(--text-light, #f3f4f6);">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title text-gradient" id="detailModalLabel">Detail Smartphone</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 text-center mb-4">
                        <img id="detailImage" src="" alt="Detail Image" class="img-fluid mb-3 rounded shadow"
                            style="max-width: 200px;">
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

<!-- Script untuk modal detail smartphone -->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Inisialisasi Radar Chart dan update data
        let radarChart;

        // Expose function untuk digunakan dari luar
        window.updateSmartphoneDetail = function(smartphone) {
            console.log('Updating smartphone detail:', smartphone); // Debugging

            // Set data detail smartphone
            document.getElementById('detailImage').src = smartphone.image_url;
            document.getElementById('detailName').textContent = smartphone.name;
            document.getElementById('detailBrand').textContent = smartphone.brand ||
                'Not specified';
            document.getElementById('detailPrice').textContent =
                `Rp ${new Intl.NumberFormat('id-ID').format(smartphone.price)}`;
            document.getElementById('detailYear').textContent = smartphone.release_year || 'N/A';
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
            document.getElementById('detailProcessor').textContent = smartphone.processor || 'N/A';

            // Pastikan nilai skor ada
            const cameraScore = parseFloat(smartphone.camera_score || 0);
            const performanceScore = parseFloat(smartphone.performance_score || 0);
            const designScore = parseFloat(smartphone.design_score || 0);
            const batteryScore = parseFloat(smartphone.battery_score || 0);

            // Set badge untuk skor
            document.getElementById('detailCameraScore').innerHTML =
                `<span class="badge bg-${getBadgeColor(cameraScore)}">${cameraScore.toFixed(1)}</span>`;
            document.getElementById('detailPerformanceScore').innerHTML =
                `<span class="badge bg-${getBadgeColor(performanceScore)}">${performanceScore.toFixed(1)}</span>`;
            document.getElementById('detailDesignScore').innerHTML =
                `<span class="badge bg-${getBadgeColor(designScore)}">${designScore.toFixed(1)}</span>`;
            document.getElementById('detailBatteryScore').innerHTML =
                `<span class="badge bg-${getBadgeColor(batteryScore)}">${batteryScore.toFixed(1)}</span>`;

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
        };

        function initializeDetailButtons() {
            const detailButtons = document.querySelectorAll('.detail-btn');

            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const smartphone = JSON.parse(this.getAttribute('data-smartphone'));
                    console.log('Smartphone data:', smartphone); // Debugging

                    window.updateSmartphoneDetail(smartphone);
                });
            });
        }

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
        function getBadgeColor(score) {
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

        // Panggil fungsi inisialisasi setelah dokumen dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initializeDetailButtons();
        });
    </script>
@endpush

@php
    function getBadgeColor($score)
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
