@extends('layouts.app')

@section('title', 'Perhitungan TOPSIS - SPK Delivery Food')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Perhitungan TOPSIS</h1>
        <form action="{{ route('calculation.calculate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-calculator"></i> Hitung Peringkat
            </button>
        </form>
    </div>
    
    @if(!$calculated)
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada hasil perhitungan. Klik tombol "Hitung Peringkat" untuk melakukan perhitungan.
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bobot Kriteria (Hasil AHP)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Kriteria</th>
                                    <th>Jenis</th>
                                    <th>Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criterias as $criteria)
                                    <tr>
                                        <td>{{ $criteria->code }}</td>
                                        <td>{{ $criteria->name }}</td>
                                        <td>
                                            @if($criteria->is_cost)
                                                <span class="badge bg-warning">Cost</span>
                                            @else
                                                <span class="badge bg-success">Benefit</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($criteria->weight, 4) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($calculated && isset($topsisData))
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Matriks Keputusan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Alternatif</th>
                                        @foreach($topsisData['criterias'] as $criteria)
                                            <th>{{ $criteria->name }} ({{ $criteria->code }})</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topsisData['alternatives'] as $i => $alternative)
                                        <tr>
                                            <td>{{ $alternative->name }}</td>
                                            @foreach($topsisData['criterias'] as $j => $criteria)
                                                <td>{{ number_format($topsisData['matrix'][$i][$j], 2) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    @if($calculated)
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Hasil Peringkat</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Alternatif</th>
                                        <th>Nilai Preferensi</th>
                                        <th>Jarak Solusi Ideal Positif</th>
                                        <th>Jarak Solusi Ideal Negatif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        <tr>
                                            <td>{{ $result->rank }}</td>
                                            <td>{{ $result->alternative->name }}</td>
                                            <td>{{ number_format($result->preference_value, 4) }}</td>
                                            <td>{{ number_format($result->positive_distance, 4) }}</td>
                                            <td>{{ number_format($result->negative_distance, 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-success mt-3">
                            <h5><i class="fas fa-trophy"></i> Aplikasi Delivery Food Terbaik:</h5>
                            @if($results->isNotEmpty())
                                <p class="h4">{{ $results->where('rank', 1)->first()->alternative->name }}</p>
                                <p>Dengan nilai preferensi: {{ number_format($results->where('rank', 1)->first()->preference_value, 4) }}</p>
                            @else
                                <p>Belum ada data hasil</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Langkah Perhitungan TOPSIS</h6>
                </div>
                <div class="card-body">
                    <p>Berikut adalah langkah-langkah metode TOPSIS:</p>
                    <ol>
                        <li>Membuat matriks keputusan</li>
                        <li>Normalisasi matriks keputusan</li>
                        <li>Mengalikan matriks normalisasi dengan bobot kriteria (dari AHP)</li>
                        <li>Menentukan solusi ideal positif dan negatif</li>
                        <li>Menghitung jarak setiap alternatif ke solusi ideal</li>
                        <li>Menghitung nilai preferensi setiap alternatif</li>
                        <li>Menentukan peringkat berdasarkan nilai preferensi</li>
                    </ol>
                    <p><strong>Catatan:</strong> Alternatif dengan nilai preferensi tertinggi adalah alternatif terbaik.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection