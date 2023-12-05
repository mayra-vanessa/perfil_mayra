<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
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

Route::get('/', function () {
    return view('welcome');
})->name('inicio');


Route::post('/logout', [UsuariosController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/registro', [UsuariosController::class, 'showRegistroForm'])->name('registro');
Route::post('/registro', [UsuariosController::class, 'registro'])->name('registro.submit');

Route::get('/login', [UsuariosController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UsuariosController::class, 'login'])->name('login.submit');

Route::get('/perfil', [UsuariosController::class, 'showPerfil'])->middleware('auth')->name('perfil');
Route::post('/perfil', [UsuariosController::class, 'updatePerfil'])->middleware('auth')->name('perfil.actualizar');