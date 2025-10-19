@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/fixes_request.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">申請一覧</h2>

        <div class="content-list">
            <div class="tabs">
                <button class="tab-button active" data-tab="pending">承認待ち</button>
                <button class="tab-button" data-tab="approved">承認済み</button>
            </div>

            <div class="tab-content active" id="pending">
                <!-- 承認待ちの勤怠 -->
                <table class="tab-content-table">
                    <tr class="tab-content-table__header">
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                    @forelse($pendingRequests as $request)
                    <tr class="tab-content-table__data">
                        <td>承認待ち</td>
                        <td>{{ $request->attendance->user->name }}</td>
                        <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
                        <td>{{ $request->request_reason }}</td>
                        <td>{{ $request->created_at->format('Y/m/d H:i') }}</td>
                        <td><a href="{{ route('admin.approval.page', ['id' => $request->attendance->id]) }}">詳細</a></td>
                    </tr>
                    @empty
                    <tr class="tab-content-table__data">
                        <td colspan="6" style="text-align: center;">承認待ちの申請はありません</td>
                    </tr>
                    @endforelse
                </table>
            </div>
            <div class="tab-content" id="approved">
                <!-- 承認済みの勤怠 -->
                <table class="tab-content-table">
                    <tr class="tab-content-table__header">
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日付</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                    @forelse($approvedRequests as $request)
                    <tr class="tab-content-table__data">
                        <td>承認済み</td>
                        <td>{{ $request->attendance->user->name }}</td>
                        <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
                        <td>{{ $request->request_reason }}</td>
                        <td>{{ $request->created_at->format('Y/m/d H:i') }}</td>
                        <td><a href="{{ route('admin.attendance.detail', ['id' => $request->attendance->id]) }}">詳細</a></td>
                    </tr>
                    @empty
                    <tr class="tab-content-table__data">
                        <td colspan="6" style="text-align: center;">承認済みの申請はありません</td>
                    </tr>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
@endsection