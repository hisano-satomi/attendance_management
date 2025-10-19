@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/approval.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">勤怠詳細</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <form action="{{ route('admin.approval', $request->id) }}" method="POST">
            @csrf
            <table class="attendance-detail-table">
                <tr class="attendance-detail-table__row">
                    <th>名前</th>
                    <td>{{ $request->attendance->user->name }}</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>日付</th>
                    <td class="attendance-detail-table__year">{{ $request->attendance->date->year }}年</td>
                    <td class="attendance-detail-table__date">{{ $request->attendance->date->month }}月{{ $request->attendance->date->day }}日</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>出勤・退勤</th>
                    <td>
                        {{ $request->work_start ? $request->work_start->format('H:i') : '-' }}
                        ～
                        {{ $request->work_stop ? $request->work_stop->format('H:i') : '-' }}
                    </td>
                </tr>
                @if($request->fixesBreakRequests->count() > 0)
                    @foreach($request->fixesBreakRequests as $index => $breakRequest)
                    <tr class="attendance-detail-table__row">
                        <th>休憩{{ $index + 1 }}</th>
                        <td>
                            {{ $breakRequest->break_start ? $breakRequest->break_start->format('H:i') : '-' }}
                            ～
                            {{ $breakRequest->break_stop ? $breakRequest->break_stop->format('H:i') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr class="attendance-detail-table__row">
                        <th>休憩</th>
                        <td></td>
                    </tr>
                @endif
                <tr class="attendance-detail-table__row">
                    <th>備考</th>
                    <td>{{ $request->request_reason }}</td>
                </tr>
            </table>

            @if($request->status === 'pending')
            <div class="attendance-detail-table__button">
                <button type="submit" class="btn btn-approval">承認</button>
            </div>
            @else
            <div class="attendance-detail-table__approved">
                <p class="approved-status">承認済み</p>
            </div>
            @endif
        </form>
    </div>
@endsection