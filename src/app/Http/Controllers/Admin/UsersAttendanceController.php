<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class UsersAttendanceController extends Controller
{
    // スタッフ一覧画面表示
    public function usersListShow()
    {
        $users = User::all();
        return view('admin.auth.users', compact('users'));
    }

    // スタッフ別勤怠一覧画面表示
    public function usersAttendanceShow()
    {
        return view('admin.auth.users_attendance');
    }
}
