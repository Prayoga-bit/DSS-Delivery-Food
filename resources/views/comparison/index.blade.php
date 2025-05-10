@extends('layouts.app')

@section('title', 'Perbandingan AHP - SPK Delivery Food')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Perbandingan Kriteria (AHP)</h1>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Matriks Perbandingan Berpasangan</h6>
        </div>
        <div class="card-body">
            @if($criterias->isEmpty())
                <div class="alert alert-warning">
                    Belum ada data kriteria. Pastikan data kriteria sudah tersedia.
                </div>
            @else
                <form action="{{ route('comparison.store') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kriteria</th>
                                    @foreach($criterias as $criteria)
                                        <th>{{ $criteria->name }} ({{ $criteria->code }})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criterias as $criteria1)
                                    <tr>
                                        <td><strong>{{ $criteria1->name }} ({{ $criteria1->code }})</strong></td>
                                        @foreach($criterias as $criteria2)
                                            <td>
                                                @if($criteria1->id == $criteria2->id)
                                                    <!-- Diagonal elements are always 1 -->
                                                    <input type="hidden" name="comparison[{{ $criteria1->id }}][{{ $criteria2->id }}]" value="1">
                                                    <span class="badge bg-secondary">1</span>
                                                @elseif($criteria1->id < $criteria2->id)
                                                    <!-- Only fill upper triangular matrix -->
                                                    <select class="form-select" name="comparison[{{ $criteria1->id }}][{{ $criteria2->id }}]" required>
                                                        <option value="">-- Pilih --</option>
                                                        @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $val)
                                                            <option value="{{ $val }}" {{ (isset($comparisonValues[$criteria1->id][$criteria2->id]) && $comparisonValues[$criteria1->id][$criteria2->id] == $val) ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <!-- Lower triangular matrix will be calculated automatically -->
                                                    @if(isset($comparisonValues[$criteria2->id][$criteria1->id]) && $comparisonValues[$criteria2->id][$criteria1->id] > 0)
                                                        <span class="badge bg-info">1/{{ $comparisonValues[$criteria2->id][$criteria1->id] }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">Auto</span>
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Hitung Bobot AHP</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Skala Perbandingan AHP</h6>
        </div>
        <div class="card-body">
            <p>Dalam pengisian matriks perbandingan, gunakan skala berikut:</p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Definisi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td>Sama penting</td>
                            <td>Kedua kriteria sama penting</td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td>Sedikit lebih penting</td>
                            <td>Kriteria yang satu sedikit lebih penting daripada yang lain</td>
                        </tr>
                        <tr>
                            <td class="text-center">5</td>
                            <td>Lebih penting</td>
                            <td>Kriteria yang satu lebih penting daripada yang lain</td>
                        </tr>
                        <tr>
                            <td class="text-center">7</td>
                            <td>Sangat lebih penting</td>
                            <td>Kriteria yang satu sangat lebih penting daripada yang lain</td>
                        </tr>
                        <tr>
                            <td class="text-center">9</td>
                            <td>Mutlak lebih penting</td>
                            <td>Kriteria yang satu mutlak lebih penting daripada yang lain</td>
                        </tr>
                        <tr>
                            <td class="text-center">2, 4, 6, 8</td>
                            <td>Nilai tengah</td>
                            <td>Nilai-nilai antara dua pertimbangan yang berdekatan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3">
                <p><strong>Catatan:</strong></p>
                <ul>
                    <li>Isi hanya bagian atas dari diagonal utama (sel berwarna putih)</li>
                    <li>Diagonal utama selalu bernilai 1 (sama penting)</li>
                    <li>Bagian bawah diagonal akan otomatis diisi dengan nilai resiprokal (1/nilai)</li>
                    <li>Jika A terhadap B bernilai 3, maka B terhadap A bernilai 1/3</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection