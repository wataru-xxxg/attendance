@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="register-title">会員登録</h1>

    <form action="/register" method="post">
        @csrf
        <div class="form-group">
            <label class="form-label">名前</label>
            <input type="text" class="form-input" name="name" value="{{ old('name') }}">
            @error('name')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

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
            <input type="password" class="form-input" name="password" value="{{ old('password') }}">
            @error('password')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">パスワード確認</label>
            <input type="password" class="form-input" name="password-confirmation">
            @error('password-confirmation')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <button type="submit" class="register-button">登録する</button>

        <div class="login-link">
            <a href="/login">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection