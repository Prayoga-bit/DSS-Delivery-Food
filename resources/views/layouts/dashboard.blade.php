@extends('layouts.app')

@section('title', 'Dashboard - SPK Delivery Food')

@section('content')
<div class="container">
    <h1 class="my-4">Dashboard</h1>
    
    <div class="row">
        <!-- Total Kriteria Card -->
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jumlah Kriteria
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $criteriaCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-ul fa-2x text-gray-300" style="color:blue"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Alternatif Card -->
        <div class="col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Jumlah Alternatif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alternativeCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-archive fa-2x text-gray-300" style="color:green"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Peringkat Aplikasi Delivery Food</h6>
                    @if($hasResults)
                        <a href="{{ route('calculation.index') }}" class="btn btn-sm btn-primary">
                            Lihat Semua Hasil
                        </a>
                    @else
                        <a href="{{ route('calculation.index') }}" class="btn btn-sm btn-primary">
                            Hitung Peringkat
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($hasResults)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Alternatif</th>
                                        <th>Nilai Preferensi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        <tr>
                                            <td>{{ $result->rank }}</td>
                                            <td>{{ $result->alternative->name }}</td>
                                            <td>{{ number_format($result->preference_value, 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                            <p>Belum ada hasil perhitungan</p>
                            <p>Untuk melakukan perhitungan, silahkan ikuti langkah-langkah berikut:</p>
                            <ol class="text-start">
                                <li>Pastikan data kriteria sudah lengkap</li>
                                <li>Tambahkan alternatif aplikasi delivery food</li>
                                <li>Isi nilai perbandingan antar kriteria (AHP)</li>
                                <li>Isi nilai alternatif untuk setiap kriteria</li>
                                <li>Lakukan perhitungan dengan metode TOPSIS</li>
                            </ol>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Penjelasan Metode AHP-TOPSIS</h6>
                </div>
                <div class="card-body">
                    <p>Sistem Pendukung Keputusan (SPK) ini menggunakan gabungan metode <strong>AHP</strong> (Analytical Hierarchy Process) dan <strong>TOPSIS</strong> (Technique for Order Preference by Similarity to Ideal Solution) untuk memilih aplikasi delivery food terbaik.</p>
                    
                    <h5>Metode AHP</h5>
                    <ul>
                        <li>AHP digunakan untuk menghitung bobot kepentingan setiap kriteria</li>
                        <li>Menggunakan perbandingan berpasangan antar kriteria</li>
                        <li>Menghasilkan nilai konsistensi untuk memastikan penilaian yang objektif</li>
                    </ul>
                    
                    <h5>Metode TOPSIS</h5>
                    <ul>
                        <li>TOPSIS digunakan untuk menentukan ranking alternatif berdasarkan kriteria</li>
                        <li>Memilih alternatif yang memiliki jarak terpendek dari solusi ideal positif dan jarak terjauh dari solusi ideal negatif</li>
                        <li>Menggunakan bobot kriteria dari hasil perhitungan AHP</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection