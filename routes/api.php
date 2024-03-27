<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Aplic\Principal\PrincipalController;
use App\Http\Controllers\Support\MaintenanceMarcadorController;
use App\Http\Controllers\Support\MaintenanceTipoMarcadorController;



Route::post('/', [PrincipalController::class, 'master']); // .../api/
Route::apiResource('/tag', MaintenanceMarcadorController::class);
Route::apiResource('/typetag', MaintenanceTipoMarcadorController::class);