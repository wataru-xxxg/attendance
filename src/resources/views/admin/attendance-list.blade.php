@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('navigation')
@include('components.admin-navigation')
@endsection

@section('content')
<div class="container">
    <div class="title-bar">
        <h1>2023年6月1日の勤怠</h1>
    </div>

    <div class="date-navigation">
        <a href="#" class="prev">
            ← 前日
        </a>
        <div class="date">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            2023/06/01
        </div>
        <a href="#" class="next">
            翌日 →
        </a>
    </div>

    <div class="attendance-table">
        <table>
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>山田 太郎</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><button class="detail-button">詳細</button></td>
                </tr>
                <tr>
                    <td>西 怜奈</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><button class="detail-button">詳細</button></td>
                </tr>
                <tr>
                    <td>増田 一世</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><button class="detail-button">詳細</button></td>
                </tr>
                <tr>
                    <td>山本 敬吾</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><button class="detail-button">詳細</button></td>
                </tr>
                <tr>
                    <td>秋田 朋美</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><button class="detail-button">詳細</button></td>
                </tr>
                <tr>
                    <td>中西 敦夫</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><button class="detail-button">詳細</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection