<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PublicacaoController;
use App\Http\Controllers\PortalAuthController;

Route::middleware(['portal.session'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
Route::get('/import', [ImportController::class, 'form'])->name('import.form');
Route::post('/import', [ImportController::class, 'import'])->name('import.run');
// CRUD de Publicações usando o modelo Test
Route::resource('publicacoes', PublicacaoController::class)->middleware('portal.session')
    ->parameters(['publicacoes' => 'publicaco']) // mapeia para o Model Test via type-hint do controller
    ->except(['show']);

// Portal auth (login antes de acessar o database)
Route::get('/portal/login', [PortalAuthController::class, 'show'])->name('portal.login');
Route::post('/portal/login', [PortalAuthController::class, 'login'])->name('portal.login.submit');
Route::post('/portal/logout', [PortalAuthController::class, 'logout'])->name('portal.logout');
Route::get('/portal/database', [PortalAuthController::class, 'gateway'])->name('portal.database');