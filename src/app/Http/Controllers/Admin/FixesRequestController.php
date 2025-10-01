<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FixesRequestController extends Controller
{
    // 申請一覧画面表示
    public function fixesRequestListShow()
    {
        return view('admin.auth.fixes_request');
    }
}
