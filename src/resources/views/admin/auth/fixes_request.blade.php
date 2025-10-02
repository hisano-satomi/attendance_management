@extends('admin.layout.after_header')

@section('content')
    <h2 class="content-title">申請一覧</h2>

    <div class="content-list">
        <div class="tabs">
            <button class="tab-button active" data-tab="pending">承認待ち</button>
            <button class="tab-button" data-tab="approved">承認済み</button>
        </div>

        <div class="tab-content active" id="pending">
            <!-- 承認待ちの勤怠をここに記述 -->
            <table class="tab-content__table">
                <tr class="tab-content__header">
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日付</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
                <tr class="tab-content__data">
                    <td>承認待ち</td>
                    <td>山田 太郎</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href="#">詳細を見る</a></td>
                </tr>
            </table>
        </div>
        <div class="tab-content" id="approved">
            <!-- 承認済みの勤怠をここに記述 -->
        </div>
    </div>
@endsection