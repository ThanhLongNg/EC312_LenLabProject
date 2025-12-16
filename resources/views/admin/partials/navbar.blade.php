<nav class="admin-navbar">
    <div class="navbar-left">
        <span class="logo">LENLAB ADMIN</span>
    </div>

    <div class="navbar-right">
        <span class="admin-name">
            {{ auth()->user()->name ?? 'Admin' }}
        </span>

        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button class="logout-btn">Đăng xuất</button>
        </form>
    </div>
</nav>