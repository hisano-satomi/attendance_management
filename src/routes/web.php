<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
// use App\Http\Controllers\AuthorController;
// use App\Http\Controllers\BookController;
// use App\Http\Controllers\SessionController;

// 管理者用
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\TimesheetController;
use App\Http\Controllers\Admin\FixesRequestController;
use App\Http\Controllers\Admin\UsersAttendanceController;
use App\Http\Controllers\Admin\ApprovalController;

// 一般ユーザー用
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\User\FixesRequestController as UserFixesRequestController;


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

// 管理者でログイン
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'loginPageShow'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'loginProcess'])->name('admin.login.post');
    Route::get('/attendances', [TimesheetController::class, 'attendanceListShow'])->name('admin.attendance.list');
    Route::get('/attendances/id', [TimesheetController::class, 'attendanceDetailShow'])->name('admin.attendance.detail');
    Route::get('/requests', [FixesRequestController::class, 'fixesRequestListShow'])->name('admin.requests.list');
    Route::get('/users', [UsersAttendanceController::class, 'usersListShow'])->name('admin.users.list');
    Route::get('/users/id/attendances', [UsersAttendanceController::class, 'usersAttendanceShow'])->name('admin.users.attendance');
    Route::get('/requests/id', [ApprovalController::class, 'approvalPageShow'])->name('admin.approval.page');
});

// 一般ユーザーでログイン（認証が必要）
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'attendancePageShow'])->name('user.attendance');
    Route::post('/attendance/work_start', [AttendanceController::class, 'workStart'])->name('user.attendance.work_start');
    Route::post('/attendance/work_stop', [AttendanceController::class, 'workStop'])->name('user.attendance.work_stop');
    Route::post('/attendance/break_start', [AttendanceController::class, 'breakStart'])->name('user.attendance.break_start');
    Route::post('/attendance/break_stop', [AttendanceController::class, 'breakStop'])->name('user.attendance.break_stop');
    Route::get('/attendance/list', [AttendanceController::class, 'attendanceListShow'])->name('user.attendance.list');
    Route::get('/attendance/detail/id', [AttendanceController::class, 'attendanceDetailShow'])->name('user.attendance.detail');
    Route::get('/stamp_correction_request/list', [UserFixesRequestController::class, 'fixesRequestListShow'])->name('user.requests.list');
});

// ログアウト（Fortify）
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('web')
    ->name('logout');

// ルートページ - 管理者ログインページにリダイレクト
Route::get('/', function () {
    return redirect()->route('admin.login');
});
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