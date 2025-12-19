<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - LENLAB</title>

    {{-- Icons --}}
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

    <style>
        :root{
            --bg1:#071021;
            --bg2:#050a14;
            --panel: rgba(255,255,255,0.06);
            --panel2: rgba(255,255,255,0.10);
            --border: rgba(255,255,255,0.14);
            --text: rgba(255,255,255,0.92);
            --muted: rgba(255,255,255,0.70);
            --brand: #fac638;
            --danger: #ff5c5c;
            --shadow: 0 18px 40px rgba(0,0,0,0.45);
            --radius: 18px;
            --font: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans", Arial;
        }
        *{ box-sizing:border-box; }
        body{
            margin:0;
            min-height:100vh;
            font-family: var(--font);
            color: var(--text);
            background:
                radial-gradient(1200px 700px at 20% 10%, rgba(250,198,56,0.18), transparent 60%),
                radial-gradient(900px 600px at 80% 20%, rgba(56,189,248,0.12), transparent 55%),
                linear-gradient(180deg, var(--bg1) 0%, var(--bg2) 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            padding: 22px;
        }

        .wrap{
            width: 100%;
            max-width: 980px;
            display:grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 18px;
        }

        .hero{
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: rgba(255,255,255,0.04);
            box-shadow: var(--shadow);
            padding: 26px;
            overflow:hidden;
            position:relative;
        }

        .hero::before{
            content:"";
            position:absolute;
            inset:-1px;
            background: radial-gradient(900px 300px at 20% 0%, rgba(250,198,56,0.22), transparent 55%);
            pointer-events:none;
        }

        .brand{
            display:flex;
            align-items:center;
            gap: 12px;
            position:relative;
            z-index:1;
        }

        .logo{
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: radial-gradient(circle at 30% 30%, rgba(250,198,56,1), rgba(250,198,56,0.25));
            display:grid;
            place-items:center;
            color:#111;
            font-weight:900;
            font-size: 18px;
        }

        .brand h1{
            margin:0;
            font-size: 18px;
            letter-spacing: 0.6px;
        }
        .brand p{
            margin:4px 0 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .hero-content{
            margin-top: 18px;
            color: var(--muted);
            line-height: 1.6;
            position:relative;
            z-index:1;
            font-size: 14px;
        }

        .hero-badges{
            margin-top: 14px;
            display:flex;
            gap: 10px;
            flex-wrap:wrap;
            position:relative;
            z-index:1;
        }
        .badge{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.05);
            color: var(--muted);
            font-size: 12px;
        }
        .badge i{ color: var(--brand); font-size: 16px; }

        .card{
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: rgba(255,255,255,0.04);
            box-shadow: var(--shadow);
            padding: 22px;
        }

        .card h2{
            margin: 0 0 6px 0;
            font-size: 18px;
        }
        .card .sub{
            margin: 0 0 18px 0;
            color: var(--muted);
            font-size: 13px;
        }

        .alert{
            border: 1px solid rgba(255,92,92,0.35);
            background: rgba(255,92,92,0.10);
            color: rgba(255,255,255,0.92);
            padding: 10px 12px;
            border-radius: 14px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .field{
            margin-bottom: 12px;
        }
        label{
            display:block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .input{
            width: 100%;
            display:flex;
            align-items:center;
            gap: 10px;
            padding: 12px 12px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.05);
        }
        .input i{
            color: rgba(255,255,255,0.65);
            font-size: 18px;
        }
        input{
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text);
            font-size: 14px;
        }
        input::placeholder{ color: rgba(255,255,255,0.45); }

        .row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap: 10px;
            margin: 10px 0 16px 0;
        }

        .remember{
            display:flex;
            align-items:center;
            gap: 8px;
            color: var(--muted);
            font-size: 13px;
        }
        .remember input{ width:auto; }

        .btn{
            width: 100%;
            border: 1px solid rgba(250,198,56,0.35);
            background: rgba(250,198,56,0.14);
            color: var(--text);
            padding: 12px 14px;
            border-radius: 14px;
            cursor:pointer;
            font-weight: 700;
            display:flex;
            align-items:center;
            justify-content:center;
            gap: 10px;
            transition: .18s ease;
        }
        .btn:hover{
            background: rgba(250,198,56,0.22);
        }

        .tiny{
            margin-top: 12px;
            color: var(--muted);
            font-size: 12px;
            text-align:center;
        }

        @media (max-width: 900px){
            .wrap{ grid-template-columns: 1fr; }
            .hero{ display:none; }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <section class="hero">
            <div class="brand">
                <div class="logo">L</div>
                <div>
                    <h1>LENLAB ADMIN</h1>
                    <p>Hệ thống quản trị đơn hàng • sản phẩm • khách hàng</p>
                </div>
            </div>

            <div class="hero-content">
                <p>
                    Đăng nhập để truy cập Dashboard quản trị. Tài khoản <b>admin</b> vào khu vực quản trị,
                    tài khoản <b>marketing</b> sẽ được điều hướng sang khu vực marketing.
                </p>
            </div>

            <div class="hero-badges">
                <span class="badge"><i class='bx bxs-shield'></i> Bảo mật phiên đăng nhập</span>
                <span class="badge"><i class='bx bxs-dashboard'></i> Giao diện hiện đại</span>
                <span class="badge"><i class='bx bxs-bolt'></i> Tối ưu thao tác</span>
            </div>
        </section>

        <section class="card">
            <h2>Đăng nhập Admin</h2>
            <p class="sub">Vui lòng nhập email & mật khẩu để tiếp tục</p>

            @if ($errors->any())
                <div class="alert">
                    <i class='bx bxs-error-circle'></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <div class="field">
                    <label>Email</label>
                    <div class="input">
                        <i class='bx bx-envelope'></i>
                        <input
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@lenlab.com"
                            autocomplete="username"
                            required
                        >
                    </div>
                </div>

                <div class="field">
                    <label>Mật khẩu</label>
                    <div class="input">
                        <i class='bx bx-lock-alt'></i>
                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                </div>

                <div class="row">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <button type="submit" class="btn">
                    <i class='bx bx-log-in'></i>
                    Đăng nhập
                </button>

                <div class="tiny">
                    Nếu không đăng nhập được, kiểm tra guard `admin` và bảng tài khoản admin.
                </div>
            </form>
        </section>
    </div>
</body>
</html>
