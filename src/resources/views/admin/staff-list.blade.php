@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff-list.css') }}">
@endsection

@section('navigation')
@include('components.admin-navigation')
@endsection

@section('content')
<div class="container">
    <h1 class="page-title">スタッフ一覧</h1>

    <div class="staff-table-wrapper">
        <table class="staff-table">
            <thead>
                <tr>
                    <th class="staff-table-header">名前</th>
                    <th class="staff-table-header">メールアドレス</th>
                    <th class="staff-table-header">月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td class="staff-table-data">{{ $user->name }}</td>
                    <td class="staff-table-data">{{ $user->email }}</td>
                    <td class="staff-table-data">
                        <a href="{{ route('admin.attendance.staff', ['id' => $user->id]) }}"
                            class="detail-btn">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection