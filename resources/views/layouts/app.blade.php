<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LENLAB') }}</title>
    
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
    </style>

    {{-- ✅ nơi nhận @push('styles') từ các view con --}}
    @stack('styles')
</head>

<body class="bg-background-dark min-h-screen">
    <div class="min-h-screen">
        @yield('content')
    </div>

    {{-- ✅ jQuery (vì checkout đang dùng $, $.get, $.ajax) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- ✅ nơi nhận @push('scripts') từ các view con --}}
    @stack('scripts')
</body>
</html>
