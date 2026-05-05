<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LIVITAP POS') }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- x-cloak harus ada di head agar modal tidak flash sebelum Alpine init --}}
    <style>[x-cloak] { display: none !important; }</style>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
        }
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #111827;
            overflow-x: hidden;
        }
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: #111827;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        #sidebar .nav-link {
            color: #9ca3af;
            font-weight: 500;
            padding: 0.75rem 1rem;
            margin: 0.2rem 0.75rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        #sidebar .nav-link i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }
        #sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.05);
            color: #ffffff;
        }
        #sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: #ffffff;
        }
        #main-content {
            min-height: 100vh;
            background-color: #f9fafb;
            width: calc(100% - var(--sidebar-width));
        }
        .top-navbar {
            height: 64px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e5e7eb;
            z-index: 100;
        }
        .sidebar-toggle { display: none; }
        .breadcrumb-item a { color: var(--primary-color); text-decoration: none; }
        .breadcrumb-item.active { color: #6b7280; }
        
        @media (max-width: 991.98px) {
            #sidebar { 
                position: fixed; 
                z-index: 1050; 
                transform: translateX(-100%); 
            }
            #sidebar.show { transform: translateX(0); }
            #main-content { width: 100%; }
            .sidebar-toggle { display: block; }
        }

        /* UI Elements Enhancements */
        .card { border: none; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); border-radius: 0.75rem; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-hover); border-color: var(--primary-hover); }
    </style>

    @stack('styles')
</head>
<body>
<div class="d-flex">
    @include('partials.sidebar')

    <div id="main-content" class="flex-grow-1">
        <!-- Top bar -->
        <div class="top-navbar px-4 d-flex align-items-center sticky-top">
            <button class="btn btn-light btn-sm sidebar-toggle me-3 border" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb mb-0">
                     <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none text-dark d-flex align-items-center p-0" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                            <span class="text-white fw-bold" style="font-size:0.8rem;">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        </div>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name ?? 'User' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item py-2" href="{{ route('settings.business') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
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
