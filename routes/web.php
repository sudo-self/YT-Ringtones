<?php

use App\Http\Controllers\RingtoneController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RingtoneController::class, 'showForm'])->name('ringtone.form');
Route::post('/convert', [RingtoneController::class, 'convert'])->name('ringtone.convert');
Route::get('/download-m4r', [RingtoneController::class, 'downloadM4R'])->name('ringtone.download.m4r');
