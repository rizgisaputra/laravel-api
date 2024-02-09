<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TodoTagController;
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


Route::post('/register',[AuthController::class,'register'])->name('register');
Route::post('/login',[AuthController::class,'login'])->name('login');

Route::middleware(['auth:api'])->group( function (){
    Route::get('/todos',[TodoController::class,'index'])->name('todo.index');
    Route::get('/todo/{id}',[TodoController::class,'show'])->name('todo.show');
    Route::post('/todos',[TodoController::class,'store'])->name('todo.store');
    Route::put('/todo/{id}', [TodoController::class, 'update'])->name('todo.update');
    Route::delete('/todo/{id}', [TodoController::class, 'destroy'])->name('todo.destroy');
    Route::put('/todo/{id}/checked', [TodoController::class, 'updateIsDone'])->name('todo.updateIsDone');

    Route::post('/tags',[TagController::class,'store'])->name('tag.store');
    Route::get('/tags',[TagController::class,'index'])->name('tag.index');
    Route::get('/tag/{id}',[TagController::class, 'show'])->name('tag.show');
    Route::put('/tag/{id}',[TagController::class,'update'])->name('tag.update');
    Route::delete('/tag/{id}',[TagController::class,'destroy'])->name('tag.delete');

    // Route::post('/todos-tags',[TodoTagController::class,'store'])->name('todo_tag.store');
    Route::get('/todos-tags',[TodoTagController::class,'index'])->name('todo_tag.index');
    Route::get('/todo-tag/{id}', [TodoTagController::class, 'show'])->name('todo_tag.show');
    Route::put('/todo-tag/{id}', [TodoTagController::class, 'update'])->name('todo-tag.update');
    Route::delete('todo-tag/{id}', [TodoTagController::class, 'destroy'])->name('todo-tag.destroy');
});



