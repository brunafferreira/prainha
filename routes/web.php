<?php

use App\Livewire\Dashboard;
use App\Livewire\Home;
use App\Livewire\ManageRecords;
use App\Livewire\PublicReservation;
use App\Livewire\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/reservar', PublicReservation::class)->name('reservations.create');
Route::get('/login', fn () => redirect()->route('home'))->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/configuracoes', Settings::class)->name('settings');
    Route::get('/{section}', ManageRecords::class)
        ->whereIn('section', ['alunos', 'turmas', 'mensalidades', 'reservas'])
        ->name('management.index');

    Route::post('/logout', function (Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});
