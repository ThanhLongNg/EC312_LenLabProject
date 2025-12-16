<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'LenLab') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    @stack('styles')
    
    <style>
        /* Basic landing page styles */
        #banner {
            min-height: 500px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
        }
        #banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
        }
        .box-left {
            position: relative;
            z-index: 2;
            padding: 0 50px;
        }
        .box-left h2 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .box-left p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        #buy-now {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        #buy-now:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-header h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .view-all {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: bold;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        .category-item {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s;
        }
        .category-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .category-item i {
            font-size: 3rem;
            color: #ff6b6b;
            margin-bottom: 20px;
        }
        
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .review-item {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stars {
            color: #ffc107;
            margin-bottom: 10px;
        }
        
        section {
            padding: 80px 0;
        }
        #product-categories {
            background: #f8f9fa;
        }
        
        /* Header Styles */
        header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        header .bg {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 50px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        .nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 30px;
        }
        .nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav a:hover {
            color: #ff6b6b;
        }
        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 5px;
            padding: 10px 0;
            min-width: 200px;
            display: none;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown-menu li {
            padding: 0;
        }
        .dropdown-menu a {
            display: block;
            padding: 10px 20px;
        }
        .user-cart {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .search-box {
            display: flex;
        }
        .search-box input {
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 5px 0 0 5px;
            outline: none;
        }
        .search-box button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
        .user-profile, .cart-icon {
            position: relative;
            cursor: pointer;
        }
        .user-profile i, .cart-icon i {
            font-size: 1.2rem;
            color: #333;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        .user-dropdown {
            right: 0;
            left: auto;
        }
    </style>
</head>
<body>
    <!-- Header -->
    @include('layouts.header')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('layouts.footer')
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>