@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="login-title">管理者ログイン</h1>

    <form action="/admin/login" method="post">
        @csrf
        <div class="form-group">
            <label class="form-label">メールアドレス</label>
            <input type="text" class="form-input" name="email" value="{{ old('email') }}">
            @error('email')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">パスワード</label>
            <input type="password" class="form-input" name="password">
            @error('password')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <button type="submit" class="login-button">管理者ログインする</button>
    </form>
</div>
@endsection