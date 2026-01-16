<?php

use App\Http\Controllers\FinanceDetailController;
use App\Http\Controllers\FinanceSnapshotController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FinanceSnapshotController::class, 'index'])->name('snapshots.index');
Route::post('/snapshots', [FinanceSnapshotController::class, 'store'])->name('snapshots.store');
Route::get('/snapshots/{snapshot}', [FinanceSnapshotController::class, 'show'])->name('snapshots.show');

Route::post('/snapshots/{snapshot}/details', [FinanceDetailController::class, 'store'])
    ->name('snapshots.details.store');
Route::put('/details/{detail}', [FinanceDetailController::class, 'update'])->name('details.update');
Route::delete('/details/{detail}', [FinanceDetailController::class, 'destroy'])->name('details.destroy');

Route::get('/reports/spending-income', [ReportController::class, 'spendingIncome'])
    ->name('reports.spending-income');
Route::get('/reports/diagrams', [ReportController::class, 'diagrams'])
    ->name('reports.diagrams');
