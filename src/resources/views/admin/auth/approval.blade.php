@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/approval.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">勤怠詳細</h2>

        <form action="">
            <table class="attendance-detail-table">
                <tr class="attendance-detail-table__row">
                    <th>名前</th>
                    <td>山田 太郎</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>日付</th>
                    <td class="attendance-detail-table__year">2023年</td>
                    <td class="attendance-detail-table__date">6月1日</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>出勤・退勤</th>
                    <td>
                        09:00
                        ～
                        18:00
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>休憩</th>
                    <td>
                        12:00
                        ～
                        13:00
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>休憩2</th>
                    <td>

                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>備考</th>
                    <td>電車遅延のため</td>
                </tr>
            </table>

            <!-- 承認済みの分岐をあとから実装 -->
            <div class="attendance-detail-table__button">
                <button type="submit" class="btn btn-approval">承認</button>
                <button type="submit" class="btn btn-approved">承認済み</button>
            </div>
        </form>
    </div>
@endsection