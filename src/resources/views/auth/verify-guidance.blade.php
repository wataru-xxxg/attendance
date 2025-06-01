@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="verification-message">
        <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p>メール認証を完了してください。</p>
    </div>

    <a href="{{ route('verification.otp') }}" class="verification-button">認証はこちらから</a>

    <form action="{{ route('verification.resend') }}" method="post">
        @csrf
        <input type="submit" value="認証メールを再送する" class="resend-button">
    </form>
</div>
@endsection