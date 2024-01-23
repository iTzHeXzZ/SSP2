<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdfController;
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
      
Route::group(['middleware' => ['auth']], function() {

});

Route::get('/', function () {
    return view('auth/login');
})->middleware('guest');

Route::middleware('auth')->group(function () {

    Route::get('/projects', 'ProjectController@index')->name('projects');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

    #Route::post('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('/projects/update/{id}', [ProjectController::class, 'update'])->name('projects.update');



    #Route::get('/projects/street', [ProjectController::class, 'street'])->name('projects.street');
    Route::get('/projects/{ort}/{postleitzahl}/street', [ProjectController::class, 'street'])->name('projects.street');

    Route::get('/projects/{ort}/{postleitzahl}/{strasse}/number', [ProjectController::class, 'number'])->name('projects.number');
    #Route::get('/projects/number', [ProjectController::class, 'number'])->name('projects.number');

    Route::get('/pdf/showForm', [PdfController::class, 'showForm'])->name('pdf.showForm');
    Route::post('/pdf/fill', [PdfController::class, 'fillPdfForm'])->name('pdf.fill');
    



});

Route::group(['middleware' => 'admin'], function () {

    Route::resource('roles', RoleController::class);

    Route::resource('users', UserController::class);

    Route::get('/assign', [ProjectController::class, 'showAssignForm'])->name('assign.form');

    Route::post('/assign', [ProjectController::class, 'assignProjectToUser'])->name('assign.project');

    Route::get('/register', [App\Http\Controllers\HomeController::class, 'index'])->name('register');

    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');

    Route::get('/get-streets/{projectId}', [ProjectController::class, 'getStreets'])->name('get.streets');

    Route::get('/get-streets-for-location-zipcode/{ort}/{postleitzahl}', [ProjectController::class, 'getStreetsForLocationZipcode'])->name('get.streets.for.location.zipcode');

    Route::post('remove-street-from-project', [ProjectController::class, 'removeStreetFromProject'])->name('remove.street.from.project');

    Route::get('/status', 'App\Http\Controllers\StatusController@index')->name('status.index');

    Route::post('/status', 'App\Http\Controllers\StatusController@store')->name('status.store');

    Route::get('/status/{user_id}/{location}', 'App\Http\Controllers\StatusController@showLocation')->name('status.showLocation');

    Route::get('/termine', [TerminController::class, 'index'])->name('termine.index');
    Route::get('/termine/create', [TerminController::class, 'create'])->name('termine.create');
    Route::post('/termine', [TerminController::class, 'store'])->name('termine.store');
});



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // Zeige das Formular für die Benutzereinstellungen an
    Route::get('/user/settings', 'App\Http\Controllers\UserController@showSettingsForm')->name('user.settings');

    // Verarbeite die Änderungen der Benutzereinstellungen
    Route::post('/user/settings', 'App\Http\Controllers\UserController@updateSettings')->name('user.settings.update');
});
