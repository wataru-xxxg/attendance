@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-otp.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="verify-title">ワンタイムパスワード認証</h1>

    <div class="verification-message">
        <p>登録していただいたメールアドレスにワンタイムパスワードを送付しました。</p>
        <p>ワンタイムパスワードを入力して認証を完了してください。</p>
    </div>

    <form action="{{ route('verification.verify') }}" method="post">
        @csrf
        <div class="form-group">
            <label class="form-label">ワンタイムパスワード</label>
            <input type="text" class="form-input" name="otp" required>
            @error('otp')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <button type="submit" class="verify-button">認証する</button>
    </form>
</div>
@endsection