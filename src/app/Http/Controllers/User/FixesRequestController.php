<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FixesRequestController extends Controller
{
    // 修正申請一覧画面表示
    public function fixesRequestListShow()
    {
        return view('user.auth.fixes_request');
    }
}
