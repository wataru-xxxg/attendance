<div class="nav-links">
    <a href="/admin/attendance/list" class="attendance-button">勤怠一覧</a>
    <a href="/admin/staff/list" class="staff-button">スタッフ一覧</a>
    <a href="/stamp_correction_request/list" class="request-button">申請一覧</a>
    @if (Auth::guard('admin')->check())
    <form class="logout-form" action="/admin/logout" method="post">
        @csrf
        <button class="logout-button">ログアウト</button>
    </form>
    @else
    <a href="/admin/login" class="login-button">ログイン</a>
    @endif
</div>