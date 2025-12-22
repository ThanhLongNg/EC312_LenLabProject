<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng ký - LENLAB</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
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
        
        .input-group {
            position: relative;
        }
        
        .input-field {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #FAC638;
            box-shadow: 0 0 0 3px rgba(250, 198, 56, 0.1);
            background: rgba(26, 26, 26, 0.9);
        }
        
        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(250, 198, 56, 0.7);
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
        
        .btn-google {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-google:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .hero-image {
            background-image: url('https://images.unsplash.com/photo-1586281010691-3d8b8c0e5b8d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
            background-size: cover;
            background-position: center;
            border-radius: 1rem;
            position: relative;
        }
        
        .hero-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(35, 30, 15, 0.8), rgba(42, 35, 24, 0.6));
            border-radius: 1rem;
        }
        
        .toggle-password {
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .toggle-password:hover {
            color: #FAC638;
        }
    </style>
</head>

<body class="bg-background-dark min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Hero Image -->
        <div class="hero-image h-48 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center z-10">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white text-2xl">yarn</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Auth Container -->
        <div class="auth-container rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-white mb-2">Tạo tài khoản mới</h1>
                <p class="text-gray-400 text-sm">Gia nhập cộng đồng yêu len thủ công bền vững.</p>
            </div>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <!-- Name Field -->
                <div class="input-group">
                    <input 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}"
                        placeholder="Họ và tên"
                        class="input-field w-full px-4 py-3 rounded-xl border-0 focus:ring-0 pr-12"
                        required 
                        autofocus 
                        autocomplete="name"
                    />
                    <span class="input-icon">
                        <span class="material-symbols-outlined text-lg">person</span>
                    </span>
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email Field -->
                <div class="input-group">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        placeholder="Email hoặc số điện thoại"
                        class="input-field w-full px-4 py-3 rounded-xl border-0 focus:ring-0 pr-12"
                        required 
                        autocomplete="username"
                    />
                    <span class="input-icon">
                        <span class="material-symbols-outlined text-lg">mail</span>
                    </span>
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div class="input-group">
                    <input 
                        id="password" 
                        type="password" 
                        name="password"
                        placeholder="Mật khẩu"
                        class="input-field w-full px-4 py-3 rounded-xl border-0 focus:ring-0 pr-12"
                        required 
                        autocomplete="new-password"
                    />
                    <span class="input-icon toggle-password" onclick="togglePassword('password')">
                        <span class="material-symbols-outlined text-lg" id="password-icon">visibility_off</span>
                    </span>
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Confirm Password Field -->
                <div class="input-group">
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation"
                        placeholder="Xác nhận mật khẩu"
                        class="input-field w-full px-4 py-3 rounded-xl border-0 focus:ring-0 pr-12"
                        required 
                        autocomplete="new-password"
                    />
                    <span class="input-icon toggle-password" onclick="togglePassword('password_confirmation')">
                        <span class="material-symbols-outlined text-lg" id="password_confirmation-icon">visibility_off</span>
                    </span>
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Register Button -->
                <button type="submit" class="btn-primary w-full py-3 rounded-xl font-semibold text-lg">
                    Đăng ký
                </button>
                
                <!-- Divider -->
                <div class="text-center text-gray-500 text-sm">
                    Hoặc đăng ký với
                </div>
                
                <!-- Google Register -->
                <a href="/auth/google" class="btn-google w-full py-3 rounded-xl font-medium text-center flex items-center justify-center gap-3 no-underline">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Google
                </a>
                
                <!-- Login Link -->
                <div class="text-center text-gray-400 text-sm">
                    Đã có tài khoản? 
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 transition-colors font-medium">
                        Đăng nhập ngay
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = 'visibility';
            } else {
                field.type = 'password';
                icon.textContent = 'visibility_off';
            }
        }
    </script>
</body>
</html>
