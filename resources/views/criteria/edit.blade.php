@extends('layouts.app')

@section('title', 'Edit Kriteria - SPK Delivery Food')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Kriteria</h1>
        <a href="{{ route('criteria.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Kriteria</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('criteria.update', $criteria) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="code" class="form-label">Kode Kriteria</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $criteria->code) }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kriteria</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $criteria->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $criteria->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label d-block">Jenis Kriteria</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_cost" id="type_benefit" value="0" {{ old('is_cost', $criteria->is_cost) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_benefit">Benefit (Semakin tinggi semakin baik)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_cost" id="type_cost" value="1" {{ old('is_cost', $criteria->is_cost) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_cost">Cost (Semakin rendah semakin baik)</label>
                    </div>
                    @error('is_cost')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection