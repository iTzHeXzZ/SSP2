<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
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

Auth::routes();

Route::get('/', function () {
    return view('auth/login');
})->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/projects', 'ProjectController@index')->name('projects');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');

Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

Route::post('/projects/{ort}/{postleitzahl}/{strasse}/number', [ProjectController::class, 'update'])->name('projects.update');

#Route::get('/projects/street', [ProjectController::class, 'street'])->name('projects.street');
Route::get('/projects/{ort}/{postleitzahl}/street', [ProjectController::class, 'street'])->name('projects.street');

Route::get('/projects/{ort}/{postleitzahl}/{strasse}/number', [ProjectController::class, 'number'])->name('projects.number');
#Route::get('/projects/number', [ProjectController::class, 'number'])->name('projects.number');


});


