<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthorController;
// use App\Http\Controllers\BookController;
// use App\Http\Controllers\SessionController;

// 管理者用
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\TimesheetController;


// 一般ユーザー用



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

// 管理者ログイン
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'loginPageShow'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'loginProcess'])->name('admin.login.post');
    Route::get('/attendances', [TimesheetController::class, 'attendanceListShow'])->name('admin.attendance.list');
    Route::get('/attendances/id', [TimesheetController::class, 'attendanceDetailShow'])->name('admin.attendance.detail');
});

// 一般ユーザーログイン
Route::prefix('user')->group(function () {
    
});

// Route::get('/', [AuthorController::class, 'index']);
// Route::get('/add', [AuthorController::class, 'add']);
// Route::post('/add', [AuthorController::class, 'create']);
// Route::get('/edit', [AuthorController::class, 'edit']);
// Route::post('/edit', [AuthorController::class, 'update']);
// Route::get('/delete', [AuthorController::class, 'delete']);
// Route::post('/delete', [AuthorController::class, 'remove']);

// Route::get('/find', [AuthorController::class, 'find']);
// Route::post('/find', [AuthorController::class, 'search']);

// Route::get('/author/{author}', [AuthorController::class, 'bind']);

// Route::get('/verror', [AuthorController::class, 'verror']);

// Route::prefix('book')->group(function () {
//     Route::get('/', [BookController::class, 'index']);
//     Route::get('/add', [BookController::class, 'add']);
//     Route::post('/add', [BookController::class, 'create']);
// });

// Route::get('/relation', [AuthorController::class, 'relate']);

// Route::get('/session', [SessionController::class, 'getSes']);
// Route::post('/session', [SessionController::class, 'postSes']);