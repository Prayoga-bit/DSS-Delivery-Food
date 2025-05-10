@extends('layouts.app')

@section('title', 'Edit Alternatif - SPK Delivery Food')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Alternatif</h1>
        <a href="{{ route('alternatives.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Alternatif</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('alternatives.update', $alternative) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="code" class="form-label">Kode Alternatif</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $alternative->code) }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Aplikasi</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $alternative->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $alternative->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="logo" class="form-label">Logo (Opsional)</label>
                    
                    @if($alternative->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $alternative->logo) }}" alt="{{ $alternative->name }}" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    @endif
                    
                    <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah logo</div>
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
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