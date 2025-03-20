@extends('layouts.app')

@section('title', 'Edit Smartphone')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>Edit Smartphone</h2>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('smartphones.update', $smartphone) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Smartphone <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $smartphone->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price"
                                name="price" value="{{ old('price', $smartphone->price) }}" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar (PNG, Maks. 1MB)</label>
                            @if ($smartphone->image)
                                <div class="mb-2">
                                    <img src="{{ $smartphone->image_url }}" alt="{{ $smartphone->name }}"
                                        class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                name="image" accept=".png">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format yang diizinkan: PNG dengan ukuran maksimal 1MB. Biarkan kosong
                                jika tidak ingin mengubah gambar.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="camera_score" class="form-label">Skor Kamera (1-10) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0" max="10"
                                class="form-control @error('camera_score') is-invalid @enderror" id="camera_score"
                                name="camera_score" value="{{ old('camera_score', $smartphone->camera_score) }}" required>
                            @error('camera_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="performance_score" class="form-label">Skor Performa (1-10) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0" max="10"
                                class="form-control @error('performance_score') is-invalid @enderror" id="performance_score"
                                name="performance_score"
                                value="{{ old('performance_score', $smartphone->performance_score) }}" required>
                            @error('performance_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="design_score" class="form-label">Skor Desain (1-10) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0" max="10"
                                class="form-control @error('design_score') is-invalid @enderror" id="design_score"
                                name="design_score" value="{{ old('design_score', $smartphone->design_score) }}" required>
                            @error('design_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="battery_score" class="form-label">Skor Baterai (1-10) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0" max="10"
                                class="form-control @error('battery_score') is-invalid @enderror" id="battery_score"
                                name="battery_score" value="{{ old('battery_score', $smartphone->battery_score) }}"
                                required>
                            @error('battery_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4">{{ old('description', $smartphone->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('smartphones.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
