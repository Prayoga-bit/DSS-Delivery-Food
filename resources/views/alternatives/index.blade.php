@extends('layouts.app')

@section('title', 'Alternatif - SPK Delivery Food')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Data Alternatif</h1>
        <a href="{{ route('alternatives.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Alternatif
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Aplikasi Delivery Food</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Aplikasi</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alternatives as $alternative)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $alternative->code }}</td>
                                <td>{{ $alternative->name }}</td>
                                <td>{{ $alternative->description }}</td>
                                <td>
                                    <a href="{{ route('alternatives.edit', $alternative) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('alternatives.destroy', $alternative) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus alternatif ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data alternatif</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Panduan Alternatif Aplikasi</h6>
        </div>
        <div class="card-body">
            <p>Alternatif adalah aplikasi delivery food yang akan dibandingkan dalam sistem ini. Beberapa contoh alternatif yang umum digunakan:</p>
            <ul>
                <li><strong>GoFood</strong> - Layanan antar makanan dari Gojek</li>
                <li><strong>GrabFood</strong> - Layanan antar makanan dari Grab</li>
                <li><strong>ShopeeFood</strong> - Layanan antar makanan dari Shopee</li>
            </ul>
            <p>Anda dapat menambahkan alternatif lain sesuai kebutuhan, seperti FoodPanda, Zomato, atau layanan delivery food lokal lainnya.</p>
        </div>
    </div>
</div>
@endsection