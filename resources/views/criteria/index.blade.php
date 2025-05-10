@extends('layouts.app')

@section('title', 'Kriteria - SPK Delivery Food')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Data Kriteria</h1>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kriteria</h6>
            <!-- Comment out the add button since criteria are fixed -->
            <!--
            <a href="{{ route('criteria.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Kriteria
            </a>
            -->
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Keterangan</th>
                            <th>Jenis</th>
                            <th>Bobot</th>
                            <!-- <th>Aksi</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($criterias as $criteria)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $criteria->code }}</td>
                                <td>{{ $criteria->name }}</td>
                                <td>{{ $criteria->description }}</td>
                                <td>
                                    @if($criteria->is_cost)
                                        <span class="badge bg-warning">Cost (Biaya)</span>
                                    @else
                                        <span class="badge bg-success">Benefit (Keuntungan)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($criteria->weight > 0)
                                        {{ number_format($criteria->weight, 4) }}
                                    @else
                                        <span class="text-muted">Belum dihitung</span>
                                    @endif
                                </td>
                                <!--<td>
                                    <a href="{{ route('criteria.edit',  $criteria) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <-- Comment out delete button since criteria are fixed -->
                                    <!--
                                    <form action="{{ route('criteria.destroy', $criteria) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    --
                                </td> -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data kriteria</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Kriteria</h6>
        </div>
        <div class="card-body">
            <p>Sistem menggunakan 6 kriteria tetap untuk penilaian aplikasi delivery food:</p>
            <ol>
                <li><strong>Pelayanan</strong> - Kualitas layanan terhadap pelanggan</li>
                <li><strong>Promo</strong> - Penawaran diskon dan promosi</li>
                <li><strong>Fitur</strong> - Kelengkapan fitur aplikasi</li>
                <li><strong>Biaya Antar</strong> - Biaya pengiriman makanan (semakin rendah semakin baik)</li>
                <li><strong>Kecepatan</strong> - Kecepatan pengantaran makanan</li>
                <li><strong>Keamanan Aplikasi</strong> - Keamanan dan privasi pengguna aplikasi</li>
            </ol>
            <p><strong>Catatan:</strong> Semua kriteria bernilai Benefit (semakin tinggi nilainya semakin baik) kecuali "Biaya Antar" yang bernilai Cost (semakin rendah nilainya semakin baik).</p>
        </div>
    </div>
</div>
@endsection