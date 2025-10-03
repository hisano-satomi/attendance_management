<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersAttendanceController extends Controller
{
    // スタッフ一覧画面表示
    public function usersListShow()
    {
        return view('admin.auth.users');
    }

    // スタッフ別勤怠一覧画面表示
    public function usersAttendanceShow()
    {
        return view('admin.auth.users_attendance');
    }
}
