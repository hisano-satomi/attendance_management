<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    // 修正申請承認画面表示
    public function approvalPageShow()
    {
        return view('admin.auth.approval');
    }
}
