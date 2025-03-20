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

    @if ($smartphones->isEmpty())
        <div class="alert alert-info" data-aos="fade-up">
            <p><i class="fas fa-info-circle me-2"></i>Belum ada data smartphone. Silakan tambahkan data smartphone baru.</p>
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
                                            <button class="btn btn-sm btn-secondary preview-btn"
                                                data-img="{{ $smartphone->image_url }}"
                                                data-name="{{ $smartphone->name }}" data-bs-toggle="tooltip"
                                                title="Preview Gambar">
                                                <i class="fas fa-eye"></i>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-black" id="imagePreviewModalLabel">Preview Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="previewImage" src="" alt="Preview Image" class="img-fluid"
                        style="max-width: 100%;">
                    <h5 id="previewTitle" class="text-black"></h5>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
            document.getElementById('goToPage').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    goToPage();
                }
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
