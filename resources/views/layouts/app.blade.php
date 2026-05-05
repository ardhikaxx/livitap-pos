<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LIVITAP POS') }}</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- x-cloak harus ada di head agar modal tidak flash sebelum Alpine init --}}
    <style>[x-cloak] { display: none !important; }</style>

    <style>
        body { overflow-x: hidden; }
        #sidebar .nav-link:hover { background-color: rgba(255,255,255,0.1); }
        #sidebar .nav-link.active { background-color: #0d6efd; }
        #main-content { min-height: 100vh; background-color: #f8f9fa; }
        .sidebar-toggle { display: none; }
        @media (max-width: 768px) {
            #sidebar { position: fixed; z-index: 1000; transform: translateX(-100%); transition: transform 0.3s; }
            #sidebar.show { transform: translateX(0); }
            .sidebar-toggle { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="d-flex">
    @include('partials.sidebar')

    <div id="main-content" class="flex-grow-1">
        <!-- Top bar -->
        <div class="bg-white border-bottom px-4 py-2 d-flex align-items-center">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle me-3" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb mb-0">
                     <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>

        @hasSection('full_content')
            @yield('full_content')
        @else
        <main class="p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
        @endif
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
