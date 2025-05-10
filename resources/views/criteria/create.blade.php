@extends('layouts.app')
@section('title', 'Tambah Kriteria - SPK Delivery Food')
@section('page-title', 'Tambah Kriteria')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Kriteria</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('criteria.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="code" class="form-label">Kode Kriteria</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                @error('code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <small class="form-text text-muted">Contoh: C1, C2, dll.</small>
            </div>
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Kriteria</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <small class="form-text text-muted">Contoh: Pelayanan, Promo, Fitur, dll.</small>
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Kriteria</label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="benefit" {{ old('type') == 'benefit' ? 'selected' : '' }}>Benefit (Makin Besar Makin Baik)</option>
                    <option value="cost" {{ old('type') == 'cost' ? 'selected' : '' }}>Cost (Makin Kecil Makin Baik)</option>
                </select>
                @error('type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi (Opsional)</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('criteria.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection