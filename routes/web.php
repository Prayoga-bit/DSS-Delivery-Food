<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\CalculationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Criteria routes
Route::resource('criteria', CriteriaController::class);

// Alternative routes
Route::resource('alternatives', AlternativeController::class);
Route::get('alternatives-scores', [AlternativeController::class, 'editScores'])->name('alternatives.scores');
Route::post('alternatives-scores', [AlternativeController::class, 'updateScores'])->name('alternatives.scores.update');

// Comparison matrix routes
Route::get('comparison', [ComparisonController::class, 'index'])->name('comparison.index');
Route::post('comparison', [ComparisonController::class, 'store'])->name('comparison.store');

// Calculation routes
Route::get('calculation', [CalculationController::class, 'index'])->name('calculation.index');
Route::post('calculation', [CalculationController::class, 'calculate'])->name('calculation.calculate');