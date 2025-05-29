<div class="nav-links">
    <a href="{{ route('attendance.index') }}" class="attendance-button">勤怠</a>
    <a href="{{ route('attendance.list') }}" class="list-button">勤怠一覧</a>
    <a href="{{ route('request.list') }}" class="request-button">申請</a>
    @if (Auth::check())
    <form class="logout-form" action="/logout" method="post">
        @csrf
        <button class="logout-button">ログアウト</button>
    </form>
    @else
    <a href="/login" class="login-button">ログイン</a>
    @endif
</div>