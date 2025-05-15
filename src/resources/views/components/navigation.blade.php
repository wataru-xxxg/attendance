<div class="nav-links">
    @if (Auth::check())
    <form class="logout-form" action="/logout" method="post">
        @csrf
        <button class="logout-button">ログアウト</button>
    </form>
    @else
    <a href="/login" class="login-button">ログイン</a>
    @endif
    <a href="/mypage" class="mypage-button">マイページ</a>
    <a href="/sell" class="listing-button">出品</a>
</div>