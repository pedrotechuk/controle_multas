<?php

use Livewire\Livewire;
use Livewire\Volt\Volt;
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

if (env('APP_ENV') === 'production') {
    Livewire::setScriptRoute(function ($handle) {
        return Route::get('gerenciamento_powerapps/livewire/livewire.js', $handle);
    });

    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('gerenciamento_powerapps/livewire/update', $handle);
    });
}

Volt::route('/', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('/erros')->group(function () {
    Route::view('/403', 'erros.403')->name('errors.403');
    Route::view('/401', 'erros.401')->name('errors.401');
});


Route::prefix('/admin')->group(function () {
    Volt::route('/exportar', 'admin.exports.index')->middleware(['auth', 'verified'])->name('admin.exports.index');

    Route::prefix('/usuarios')->group(function () {
        Volt::route('/', 'admin.users.index')->middleware(['auth', 'verified'])->name('admin.users.index');
        Volt::route('/novo', 'admin.users.create')->middleware(['auth', 'verified'])->name('admin.users.create');
        Volt::route('/editar/{id}', 'admin.users.update')->middleware(['auth', 'verified'])->name('admin.users.update');
    });

    Route::prefix('/perfis')->group(function () {
        Volt::route('/', 'admin.profile.index')->middleware(['auth', 'verified'])->name('admin.profile.index');
        // Volt::route('/novo', 'admin.users.create')->middleware(['auth', 'verified'])->name('admin.users.create');
        Volt::route('/editar/{id}', 'admin.profile.update')->middleware(['auth', 'verified'])->name('admin.profile.update');
        Volt::route('/editar-permissoes/{id}', 'admin.profile.update-permissions')->middleware(['auth', 'verified'])->name('admin.profile.update-permissions');
    });
});

Route::prefix('/multas')->group(function (){
    Volt::route('/finalizar/{id}', 'multas.finalize')->middleware(['auth', 'verified'])->name('multas.finalize');
    Volt::route('/editar/{id}', 'multas.update')->middleware(['auth', 'verified'])->name('multas.update');
});

Route::prefix('/consultas')->group(function (){
    Volt::route('/', 'consultas.index')->middleware(['auth', 'verified'])->name('consultas.index');
    Volt::route('/info/{id}', 'consultas.info')->middleware(['auth', 'verified'])->name('consultas.info');

});

require __DIR__ . '/auth.php';
