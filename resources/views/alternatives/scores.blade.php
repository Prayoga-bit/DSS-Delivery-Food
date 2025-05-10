@extends('layouts.app')

@section('title', 'Nilai Alternatif - SPK Delivery Food')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nilai Alternatif</h1>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Isi Nilai Alternatif untuk Setiap Kriteria</h6>
        </div>
        <div class="card-body">
            @if($alternatives->isEmpty())
                <div class="alert alert-warning">
                    Belum ada data alternatif. Silahkan <a href="{{ route('alternatives.create') }}">tambahkan alternatif</a> terlebih dahulu.
                </div>
            @elseif($criterias->isEmpty())
                <div class="alert alert-warning">
                    Belum ada data kriteria. Pastikan data kriteria sudah tersedia.
                </div>
            @else
                <form action="{{ route('alternatives.scores.update') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align: middle;">Alternatif</th>
                                    <th colspan="{{ $criterias->count() }}" class="text-center">Kriteria</th>
                                </tr>
                                <tr>
                                    @foreach($criterias as $criteria)
                                        <th>
                                            {{ $criteria->name }}
                                            <div class="small text-muted">{{ $criteria->code }}</div>
                                            @if($criteria->is_cost)
                                                <span class="badge bg-warning small">Cost</span>
                                            @else
                                                <span class="badge bg-success small">Benefit</span>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alternatives as $alternative)
                                    <tr>
                                        <td>
                                            <strong>{{ $alternative->name }}</strong>
                                            <div class="small text-muted">{{ $alternative->code }}</div>
                                        </td>
                                        @foreach($criterias as $criteria)
                                            <td>
                                                <input 
                                                type="number" 
                                                    class="form-control" 
                                                    name="scores[{{ $alternative->id }}][{{ $criteria->id }}]" 
                                                    value="{{ $scores[$alternative->id][$criteria->id] ?? '' }}"
                                                    step="0.01"
                                                    min="0"
                                                    @if(!$criteria->is_cost)
                                                    max="100"
                                                    @else
                                                    max="1000000"
                                                    @endif
                                                    required
                                                >
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Panduan Pengisian</h6>
        </div>
        <div class="card-body">
            <p>Petunjuk pengisian nilai alternatif:</p>
            <ul>
                <li>Isi nilai untuk setiap alternatif pada masing-masing kriteria</li>
                <li>Gunakan skala 0-100 atau nilai yang sesuai dengan data aktual</li>
                <li>Untuk kriteria <strong>Benefit</strong> (semakin tinggi semakin baik): nilai lebih tinggi berarti lebih baik</li>
                <li>Untuk kriteria <strong>Cost</strong> (semakin rendah semakin baik): nilai lebih rendah berarti lebih baik</li>
                <li>Contoh: Untuk kriteria "Biaya Antar", masukkan nilai sesuai biaya aktual (misal: 10000, 15000, dll)</li>
                <li>Nilai harus berupa angka dan tidak boleh kosong</li>
            </ul>
        </div>
    </div>
</div>
@endsection