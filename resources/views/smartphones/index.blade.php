@extends('layouts.app')

@section('title', 'Daftar Smartphone')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Daftar Smartphone</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('smartphones.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Smartphone
            </a>
        </div>
    </div>

    @if ($smartphones->isEmpty())
        <div class="alert alert-info">
            <p>Belum ada data smartphone. Silakan tambahkan data smartphone baru.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Kamera</th>
                        <th>Performa</th>
                        <th>Desain</th>
                        <th>Baterai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($smartphones as $index => $smartphone)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ $smartphone->image_url }}" alt="{{ $smartphone->name }}" class="img-thumbnail"
                                    style="max-width: 50px;">
                            </td>
                            <td>{{ $smartphone->name }}</td>
                            <td>Rp {{ number_format($smartphone->price, 0, ',', '.') }}</td>
                            <td>{{ number_format($smartphone->camera_score, 1) }}</td>
                            <td>{{ number_format($smartphone->performance_score, 1) }}</td>
                            <td>{{ number_format($smartphone->design_score, 1) }}</td>
                            <td>{{ number_format($smartphone->battery_score, 1) }}</td>
                            <td>
                                <a href="{{ route('smartphones.edit', $smartphone) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('smartphones.destroy', $smartphone) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
