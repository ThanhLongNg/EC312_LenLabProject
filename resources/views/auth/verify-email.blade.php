<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Xác thực email - LENLAB</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638",
                        "background-light": "#f8f8f5",
                        "background-dark": "#231e0f",
                        "surface-dark": "#2a2318", 
                        "surface-light": "#ffffff"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Spline Sans', sans-serif;
            background: linear-gradient(135deg, #231e0f 0%, #2a2318 100%);
            min-height: 100vh;
        }
        
        .auth-container {
            background: linear-gradient(135deg, #2a2318 0%, #1a1610 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(250, 198, 56, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FAC638, #f59e0b);
            color: #231e0f;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(250, 198, 56, 0.3);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="bg-background-dark min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Auth Container -->
        <div class="auth-container rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">mail</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Xác thực email</h1>
                <p class="text-gray-400 text-sm">
                    Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email bằng cách nhấp vào liên kết chúng tôi vừa gửi cho bạn.
                </p>
            </div>
            
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm text-center">
                    Một liên kết xác thực mới đã được gửi đến địa chỉ email bạn đã cung cấp.
                </div>
            @endif

            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full py-3 rounded-xl font-semibold">
                        Gửi lại email xác thực
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary w-full py-3 rounded-xl font-semibold">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
