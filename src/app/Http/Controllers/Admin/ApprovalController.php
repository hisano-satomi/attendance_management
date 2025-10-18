<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixesAttendanceRequest;

class ApprovalController extends Controller
{
    // 申請一覧画面表示
    public function fixesRequestListShow()
    {
        $fixesRequests = FixesAttendanceRequest::all();
        return view('admin.auth.fixes_request', compact('fixesRequests'));
    }

    // 修正申請承認画面表示
    public function approvalPageShow()
    {
        return view('admin.auth.approval');
    }
}
