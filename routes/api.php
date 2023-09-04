<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/annotations/create', [App\Http\Controllers\AnnotationAPIController::class, 'store'])->name('annotation.store');
Route::post('/annotations/update', [App\Http\Controllers\AnnotationAPIController::class, 'update'])->name('annotation.update');
Route::post('/annotations/delete', [App\Http\Controllers\AnnotationAPIController::class, 'delete'])->name('annotation.delete');
Route::post('/annotations/all', [App\Http\Controllers\AnnotationAPIController::class, 'getAllByCanvasId'])->name('annotation.getAllByCanvasId');
