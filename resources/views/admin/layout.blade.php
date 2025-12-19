<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LENLAB ADMIN</title>

    {{-- CSS ADMIN --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    {{-- ICONS --}}
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

    @stack('styles')
</head>

<body>

    {{-- NAVBAR --}}
    @include('admin.partials.navbar')

    <div class="app-content">
        {{-- SIDEBAR --}}
        @include('admin.partials.sidebar')

        {{-- MAIN CONTENT --}}
        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    {{-- JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('scripts')

</body>

</html>