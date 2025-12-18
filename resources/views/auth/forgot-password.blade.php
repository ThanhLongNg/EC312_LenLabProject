<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quên mật khẩu - LENLAB</title>
    
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
        
        .input-field {
            background: rgba(250, 198, 56, 0.05);
            border: 1px solid rgba(250, 198, 56, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #FAC638;
            box-shadow: 0 0 0 3px rgba(250, 198, 56, 0.1);
            background: rgba(250, 198, 56, 0.1);
        }
        
        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.5);
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
    </style>
</head>

<body class="bg-background-dark min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Hero Image -->
        <div class="hero-image h-48 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center z-10">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white text-2xl">lock_reset</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Auth Container -->
        <div class="auth-container rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-white mb-2">Quên mật khẩu?</h1>
                <p class="text-gray-400 text-sm">Không sao cả! Nhập email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu.</p>
            </div>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm text-center">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div class="relative">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        placeholder="Nhập địa chỉ email của bạn"
                        class="input-field w-full px-4 py-3 rounded-xl border-0 focus:ring-0 pr-12"
                        required 
                        autofocus
                    />
                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-primary/70">
                        <span class="material-symbols-outlined text-lg">mail</span>
                    </span>
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn-primary w-full py-3 rounded-xl font-semibold text-lg">
                    Gửi link đặt lại mật khẩu
                </button>
                
                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 transition-colors font-medium text-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Quay lại đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
