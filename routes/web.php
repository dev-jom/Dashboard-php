<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/import', [ImportController::class, 'form'])->name('import.form');
Route::post('/import', [ImportController::class, 'import'])->name('import.run');