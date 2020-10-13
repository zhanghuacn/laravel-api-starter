<?php

use App\Http\Controllers\Api\PostsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->prefix('v1')->group(function () {
    Route::get('/posts',[PostsController::class, 'index'])->name('posts.index');
    Route::get('/posts/{id}',[PostsController::class, 'show'])->name('posts.show');
    Route::post('/posts',[PostsController::class, 'store'])->name('posts.store');
});
