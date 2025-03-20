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
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($smartphones as $index => $smartphone)
                                <tr class="align-middle" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
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
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('smartphones.edit', $smartphone) }}"
                                                class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('smartphones.destroy', $smartphone) }}" method="POST"
                                                class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
        });
    </script>
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
