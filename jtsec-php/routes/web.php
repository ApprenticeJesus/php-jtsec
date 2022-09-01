<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [App\Http\Controllers\PruebasController::class, 'testOrm']);

//RUTAS DEL API

    //Rutas de prueba
    Route::get('/usuario/pruebas', [App\Http\Controllers\UserController::class, 'pruebas']);
    Route::get('/proyecto/pruebas', [App\Http\Controllers\ProjectController::class, 'pruebas']);
    Route::get('/actividad/pruebas', [App\Http\Controllers\ActivityController::class, 'pruebas']);
    Route::get('/incidencia/pruebas', [App\Http\Controllers\IncidenceController::class, 'pruebas']);

    //Rutas de controladores
    
    Route::post('/api/asignarProyecto', [App\Http\Controllers\ProjectController::class, 'assignProject']);
    Route::post('/api/asignarActividad', [App\Http\Controllers\ActivityController::class, 'assignActivity']);
    Route::post('/api/asignarIncidencia', [App\Http\Controllers\IncidenceController::class, 'assignIncidence']);
    Route::post('/api/nuevaActividad', [App\Http\Controllers\ProjectController::class, 'addActivity']);
    Route::post('/api/nuevaIncidencia', [App\Http\Controllers\ActivityController::class, 'addIncidence']);

    Route::get('/api/user/listarActividades/{id}', [App\Http\Controllers\UserController::class, 'getActivitiesByUser']);
    Route::get('/api/project/listarUsuarios/{id}', [App\Http\Controllers\ProjectController::class, 'getUsersByProject']);
    Route::get('/api/listarIncidenciasPermitidas/{id}', [App\Http\Controllers\ActivityController::class, 'listGrantedIncidences']);