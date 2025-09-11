<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PublicacaoController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/import', [ImportController::class, 'form'])->name('import.form');
Route::post('/import', [ImportController::class, 'import'])->name('import.run');
// CRUD de Publicações usando o modelo Test
Route::resource('publicacoes', PublicacaoController::class)
    ->parameters(['publicacoes' => 'publicaco']) // mapeia para o Model Test via type-hint do controller
    ->except(['show']);