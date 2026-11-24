<?php

use App\Http\Controllers\AntreanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AntreanController::class, 'index'])->name('antrean.index');
Route::get('/api/antrean-data', [AntreanController::class, 'getTableData'])->name('antrean.data');
Route::post('/api/antrean/{id}/checkin', [AntreanController::class, 'checkIn'])->name('antrean.checkin');
Route::post('/api/antrean/{id}/batal', [AntreanController::class, 'batal'])->name('antrean.batal');
