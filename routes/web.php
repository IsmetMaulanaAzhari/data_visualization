<?php

use App\Http\Controllers\StudentProductivityController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/weather');

// Weather API Routes (Open-Meteo)
Route::prefix('weather')->name('weather.')->group(function () {
    Route::get('/', [WeatherController::class, 'dashboard'])->name('dashboard');
    Route::get('/cities', [WeatherController::class, 'cities'])->name('cities');
    Route::get('/forecast', [WeatherController::class, 'forecast'])->name('forecast');
    Route::get('/comparison', [WeatherController::class, 'comparison'])->name('comparison');
    Route::get('/refresh', [WeatherController::class, 'refresh'])->name('refresh');
});

// Student Dataset Routes (CSV)
Route::prefix('student-productivity')->name('student-productivity.')->group(function () {
    Route::get('/', [StudentProductivityController::class, 'dashboard'])->name('dashboard');
    Route::post('/upload', [StudentProductivityController::class, 'upload'])->name('upload');
    Route::post('/refresh', [StudentProductivityController::class, 'refresh'])->name('refresh');
    Route::get('/api', [StudentProductivityController::class, 'apiData'])->name('api');
});
