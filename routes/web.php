<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Rutas protegidas para el Administrador
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Rutas para la gestión de Servicios (Catálogo)
    Route::get('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [\App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('services.store');
    Route::delete('/services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');

    // Rutas para supervisión de citas (Fase 3)
    Route::get('/appointments', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointments.index');
});

// Rutas protegidas para el Barbero
Route::middleware(['auth', 'role:barbero'])->prefix('barber')->name('barber.')->group(function () {
    // Rutas para la gestión de Horarios (Disponibilidad)
    Route::get('/schedules', [\App\Http\Controllers\Barber\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [\App\Http\Controllers\Barber\ScheduleController::class, 'store'])->name('schedules.store');

    // Rutas para visualizar agenda del barbero (Fase 3)
    Route::get('/appointments', [\App\Http\Controllers\Barber\AppointmentController::class, 'index'])->name('appointments.index');
});

// Rutas protegidas para el Cliente (Fase 3)
Route::middleware(['auth', 'role:cliente'])->prefix('client')->name('client.')->group(function () {
    // Ver mis citas a futuro
    Route::get('/appointments', [\App\Http\Controllers\Client\AppointmentController::class, 'index'])->name('appointments.index');
    // Formulario de agendamiento
    Route::get('/appointments/create', [\App\Http\Controllers\Client\AppointmentController::class, 'create'])->name('appointments.create');
    // Para cargar horarios disponibles vía AJAX (o fetch)
    Route::get('/appointments/available-slots', [\App\Http\Controllers\Client\AppointmentController::class, 'getAvailableSlots'])->name('appointments.available-slots');
    // Guardar la cita
    Route::post('/appointments', [\App\Http\Controllers\Client\AppointmentController::class, 'store'])->name('appointments.store');
});
