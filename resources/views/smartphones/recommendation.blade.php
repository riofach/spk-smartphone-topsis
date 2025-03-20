@extends('layouts.app')

@section('title', 'Rekomendasi Smartphone')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>Rekomendasi Smartphone</h2>
            <p class="text-muted">Masukkan kriteria dan budget untuk mendapatkan rekomendasi smartphone terbaik berdasarkan
                metode TOPSIS.</p>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('recommendation.result') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Range Budget</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_budget" class="form-label">Budget Minimum (Rp) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('min_budget') is-invalid @enderror"
                                        id="min_budget" name="min_budget" value="{{ old('min_budget', 1000000) }}"
                                        min="0" required>
                                    @error('min_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_budget" class="form-label">Budget Maksimum (Rp) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_budget') is-invalid @enderror"
                                        id="max_budget" name="max_budget" value="{{ old('max_budget', 10000000) }}"
                                        min="0" required>
                                    @error('max_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Tingkat Kepentingan Kriteria</h5>
                                <p class="text-muted small">Berikan nilai kepentingan untuk setiap kriteria (1-10). Semakin
                                    tinggi nilai, semakin penting kriteria tersebut.</p>
                            </div>

                            @foreach ($criteria as $criterion)
                                <div class="col-md-6 mb-3">
                                    <label for="criteria_weights_{{ $criterion->code }}"
                                        class="form-label">{{ $criterion->name }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="range" class="form-range" min="1" max="10"
                                            step="1" id="criteria_weights_{{ $criterion->code }}_range"
                                            value="{{ old('criteria_weights.' . $criterion->code, 5) }}"
                                            oninput="document.getElementById('criteria_weights_{{ $criterion->code }}').value = this.value">
                                        <input type="number"
                                            class="form-control @error('criteria_weights.' . $criterion->code) is-invalid @enderror"
                                            id="criteria_weights_{{ $criterion->code }}"
                                            name="criteria_weights[{{ $criterion->code }}]"
                                            value="{{ old('criteria_weights.' . $criterion->code, 5) }}" min="1"
                                            max="10" style="width: 80px;"
                                            oninput="document.getElementById('criteria_weights_{{ $criterion->code }}_range').value = this.value"
                                            required>
                                        @error('criteria_weights.' . $criterion->code)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">{{ $criterion->description }}</small>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Cari Rekomendasi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
