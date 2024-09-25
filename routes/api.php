<?php

use App\Http\Controllers\ApirequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/e_materai_data', [ApirequestController::class, 'generate_e_materai']);
