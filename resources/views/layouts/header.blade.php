<header>
    <div class="bg">
        <!-- Logo -->
        <div class="logo">
            <a href="/" style="text-decoration: none; color: inherit;">Len Lab</a>
        </div>

        <!-- Navigation -->
        <nav class="nav">
            <ul>
                <li><a href="/">Trang chủ</a></li>
                <li class="dropdown">
                    <a href="/san-pham">Sản phẩm</a>
                    <ul class="dropdown-menu">
                        <li><a href="/san-pham?search=Nguyên phụ liệu">Nguyên phụ liệu</a></li>
                        <li><a href="/san-pham?search=Đồ trang trí">Đồ trang trí</a></li>
                        <li><a href="/san-pham?search=Thời trang len">Thời trang len</a></li>
                        <li><a href="/san-pham?search=Combo tự làm">Combo tự làm</a></li>
                        <li><a href="/san-pham?search=Sách hướng dẫn">Sách hướng dẫn</a></li>
                        <li><a href="/san-pham?search=Thú bông len">Thú bông len</a></li>
                    </ul>
                </li>
                <li><a href="/gioi-thieu">Giới thiệu</a></li>
            </ul>
        </nav>

        <!-- Search & User Actions -->
        <div class="user-cart">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm...">
                <button type="button" id="searchBtn"><i class="fas fa-search"></i></button>
            </div>

            <!-- Cart -->
            <div class="cart-icon" onclick="window.location.href='/gio-hang'">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cartCount">0</span>
            </div>

            <!-- User Profile -->
            @auth
                <div class="user-profile dropdown">
                    <i class="fas fa-user"></i>
                    <ul class="dropdown-menu user-dropdown">
                        <li><a href="/profile">Thông tin cá nhân</a></li>
                        <li><a href="/orders">Đơn hàng của tôi</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Đăng xuất
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <div class="user-profile">
                    <a href="{{ route('login') }}" style="text-decoration: none; color: inherit;">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Search functionality
    $("#searchBtn").click(function() {
        const keyword = $("#searchInput").val().trim();
        if (keyword) {
            window.location.href = `/san-pham?search=${encodeURIComponent(keyword)}`;
        }
    });

    $("#searchInput").keypress(function(e) {
        if (e.which === 13) { // Enter key
            $("#searchBtn").click();
        }
    });

    // Update cart count
    function updateCartCount() {
        @auth
            $.get('/api/cart', function(response) {
                const count = response.cart.reduce((sum, item) => sum + item.quantity, 0);
                $("#cartCount").text(count);
            });
        @endauth
    }

    updateCartCount();
    
    // Update cart count every 30 seconds
    setInterval(updateCartCount, 30000);
});
</script>