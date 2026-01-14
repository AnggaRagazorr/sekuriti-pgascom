<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Security\PatrolController;
use App\Http\Controllers\Security\DailyReportController;
use App\Http\Controllers\Security\CarpoolController;
use App\Http\Controllers\Security\DocumentLogController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\Security\PatrolSubmitController;
use Symfony\Component\HttpKernel\HttpCache\Store;
use App\Http\Controllers\Monitoring\PatrolLogController;
use App\Http\Controllers\Security\PatrolReportController;


Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'monitoring'
            ? redirect()->route('monitoring.dashboard')
            : redirect()->route('security.dashboard');
    }

    return redirect()->route('login');
});


Route::get('/dashboard', function () {
    return auth()->user()->role === 'monitoring'
        ? redirect()->route('monitoring.dashboard')
        : redirect()->route('security.dashboard');
})->middleware('auth')->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'role:security'])->prefix('security')->group(function () {
    Route::view('/dashboard', 'security.dashboard')->name('security.dashboard');

    Route::get('/patrol', [PatrolController::class, 'index'])->name('security.patrol');
    Route::get('/daily-report', [DailyReportController::class, 'index'])->name('security.daily-report');
    Route::get('/carpool', [CarpoolController::class, 'index'])->name('security.carpool');
    Route::get('/document-log', [DocumentLogController::class, 'index'])->name('security.document-log');
    Route::post('/patrol/submit', [PatrolSubmitController::class,'Store'])->name('security.patrol.submit');
    Route::post('/patrol/report', [PatrolReportController::class, 'store'])->name('security.patrol.report');
});


Route::middleware(['auth', 'role:monitoring'])->prefix('monitoring')->group(function () {
    Route::view('/dashboard', 'monitoring.dashboard')->name('monitoring.dashboard');

     Route::get('/patrols', [PatrolLogController::class, 'index'])->name('monitoring.patrols.index');
    Route::get('/patrols/{patrolSubmission}', [PatrolLogController::class, 'show'])->name('monitoring.patrols.show');
});

Route::post('/geo/reverse', [GeoController::class, 'reverse'])
    ->middleware('auth')
    ->name('geo.reverse');


require __DIR__.'/auth.php';
