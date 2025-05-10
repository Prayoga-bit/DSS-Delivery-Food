@extends('layouts.app')

@section('title', 'Detail Alternatif - SPK Delivery Food')
@section('page-title', 'Detail Alternatif')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Alternatif</h6>
    </div>
    <div class="card-body">
        <p><strong>Kode Alternatif:</strong> {{ $alternative->code }}</p>
        <p><strong>Nama Aplikasi:</strong> {{ $alternative->name }}</p>
        <p><strong>Deskripsi:</strong> {{ $alternative->description ?: 'Tidak ada deskripsi.' }}</p>

        <a href="{{ route('alternatives.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
    </div>
</div>
@endsection
