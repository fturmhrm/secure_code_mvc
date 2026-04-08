<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Secure Ticketing') - SMK Wikrama Bogor</title>

    {{-- Prevent sidebar transition flash on page load --}}
    <script>
        // Apply sidebar state IMMEDIATELY before render to prevent animation flash
        (function() {
            if (window.innerWidth >= 992 && localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed-on-load');
            }
        })();
    </script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 0px;
            --topbar-height: 56px;
            --primary-color: #0d6efd;
            --sidebar-bg: #0d6efd;
            --sidebar-hover: rgba(255,255,255,0.15);
            --sidebar-active: rgba(255,255,255,0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ============================================ */
        /* SIDEBAR STYLES */
        /* ============================================ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #0d6efd 0%, #0b5ed7 100%);
            color: #fff;
            z-index: 1000;
            transition: transform 0.3s ease, width 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        /* Apply collapsed state instantly on page load (no animation) */
        html.sidebar-collapsed-on-load .sidebar {
            transform: translateX(-100%);
            transition: none;
        }
        html.sidebar-collapsed-on-load .main-wrapper {
            margin-left: 0;
            transition: none;
        }
        /* Re-enable transitions after page load */
        html.sidebar-ready .sidebar,
        html.sidebar-ready .main-wrapper {
            transition: transform 0.3s ease, margin-left 0.3s ease, width 0.3s ease;
        }

        .sidebar-header {
            padding: 1rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: var(--topbar-height);
        }

        .sidebar-header .logo {
            font-size: 1.1rem;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-header .logo:hover {
            color: rgba(255,255,255,0.9);
        }

        .sidebar-close {
            background: none;
            border: none;
            color: rgba(255,255,255,0.7);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.25rem;
            display: block;
        }

        .sidebar-close:hover {
            color: #fff;
        }

        .sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar-body::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-body::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .sidebar-body::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.4);
        }

        /* Navigation Menu */
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-section {
            padding: 0.75rem 1rem 0.4rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }

        .nav-item {
            margin: 1px 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            font-size: 0.85rem;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            font-weight: 500;
        }

        .nav-link i {
            width: 1.25rem;
            font-size: 0.9rem;
            margin-right: 0.5rem;
            text-align: center;
        }

        /* Dropdown/Collapse in Sidebar */
        .nav-link[data-bs-toggle="collapse"] {
            position: relative;
        }

        .nav-link[data-bs-toggle="collapse"]::after {
            content: '\F282';
            font-family: 'bootstrap-icons';
            position: absolute;
            right: 0.75rem;
            font-size: 0.7rem;
            transition: transform 0.2s ease;
            opacity: 0.7;
        }

        .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        .nav-collapse {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-collapse .nav-link {
            padding-left: 2.25rem;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.8);
        }

        .nav-collapse .nav-link:hover {
            color: #fff;
        }

        /* Color variants for nav links */
        .nav-link.text-danger { color: #ffb3b3 !important; }
        .nav-link.text-danger:hover { background: rgba(255,179,179,0.2); color: #fff !important; }
        .nav-link.text-success { color: #90EE90 !important; }
        .nav-link.text-success:hover { background: rgba(144,238,144,0.2); color: #fff !important; }
        .nav-link.text-warning { color: #ffe066 !important; }
        .nav-link.text-warning:hover { background: rgba(255,224,102,0.2); color: #fff !important; }
        .nav-link.text-info { color: #87CEEB !important; }
        .nav-link.text-info:hover { background: rgba(135,206,235,0.2); color: #fff !important; }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.2);
            font-size: 0.7rem;
            color: rgba(255,255,255,0.6);
            text-align: center;
        }

        /* ============================================ */
        /* MAIN CONTENT AREA */
        /* ============================================ */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        .main-wrapper.expanded {
            margin-left: 0;
        }

        /* Top Bar */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #0d6efd;
            cursor: pointer;
            padding: 0.25rem;
        }

        .sidebar-toggle:hover {
            color: #0b5ed7;
        }

        .breadcrumb-wrapper {
            display: flex;
            align-items: center;
        }

        .breadcrumb {
            font-size: 0.875rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 1.25rem;
        }

        /* Footer */
        .main-footer {
            background: #212529;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }

        .main-footer p {
            margin: 0;
            font-size: 0.85rem;
        }

        .main-footer .badge {
            font-size: 0.65rem;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ============================================ */
        /* RESPONSIVE / MOBILE */
        /* ============================================ */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-close {
                display: block;
            }

            .main-wrapper {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 992px) {
            .sidebar.collapsed {
                transform: translateX(-100%);
            }

            .main-wrapper.expanded {
                margin-left: 0;
            }
        }

        /* ============================================ */
        /* UTILITY STYLES */
        /* ============================================ */
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .card-header {
            font-weight: 600;
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        pre code {
            font-size: 0.85rem;
        }

        .btn-sqli-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
        }

        .btn-sqli-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            color: white;
        }

        .badge-vulnerable { background-color: #dc3545; }
        .badge-secure { background-color: #198754; }

        .text-success-emphasis { color: #0a3622 !important; }

        .table code {
            background-color: rgba(0,0,0,0.05);
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
            font-size: 0.85em;
        }

        .alert {
            border-left-width: 4px;
        }

        .table-responsive {
            font-size: 0.9rem;
        }

        .alert-danger {
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0%, 100% { border-left-color: #dc3545; }
            50% { border-left-color: #ff6b6b; }
        }
    </style>

    @stack('styles')
</head>

<body>
    {{-- ============================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================ --}}
    <aside class="sidebar" id="sidebar">
        {{-- Sidebar Header --}}
        <div class="sidebar-header">
            <a href="{{ url('/') }}" class="logo">
                <i class="bi bi-shield-lock"></i> Secure Ticketing
            </a>
            <button class="sidebar-close" id="sidebarClose" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Sidebar Body (Scrollable) --}}
        <div class="sidebar-body">
            <ul class="sidebar-nav">

                {{-- MAIN --}}
                <li class="nav-section">Main</li>

                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="bi bi-house"></i> Home
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                        <i class="bi bi-ticket-detailed"></i> Tickets
                    </a>
                </li>

                <li class="nav-section">Demo</li>

                {{-- Demo Blade --}}
                <li class="nav-item">
                    <a href="#demoBlade" class="nav-link {{ request()->routeIs('demo-blade.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('demo-blade.*') ? 'true' : 'false' }}">
                        <i class="bi bi-code-slash"></i> Demo Blade
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('demo-blade.*') ? 'show' : '' }}" id="demoBlade">
                        <li class="nav-item">
                            <a href="{{ route('demo-blade.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('demo-blade.directives') }}" class="nav-link">
                                <i class="bi bi-signpost-split"></i> Directives
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('demo-blade.components') }}" class="nav-link">
                                <i class="bi bi-puzzle"></i> Components
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('demo-blade.includes') }}" class="nav-link">
                                <i class="bi bi-box-arrow-in-right"></i> Include & Each
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('demo-blade.stacks') }}" class="nav-link">
                                <i class="bi bi-stack"></i> Stacks & Push
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Error Handling Demo - Minggu 5 Hari 2 --}}
                <li class="nav-item">
                    <a href="{{ route('error-handling-demo') }}" class="nav-link {{ request()->routeIs('error-handling-demo') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-triangle"></i> Error Handling Demo
                    </a>
                </li>

                {{-- SECURITY LABS --}}
                <li class="nav-section">Security Labs</li>

                {{-- XSS Lab --}}
                <li class="nav-item">
                    <a href="#xssLab" class="nav-link {{ request()->routeIs('xss-lab.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('xss-lab.*') ? 'true' : 'false' }}">
                        <i class="bi bi-shield-exclamation"></i> XSS Lab
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('xss-lab.*') ? 'show' : '' }}" id="xssLab">
                        <li class="nav-item">
                            <a href="{{ route('xss-lab.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('xss-lab.reflected.vulnerable') }}" class="nav-link text-danger">
                                <i class="bi bi-unlock"></i> Reflected (Vuln)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('xss-lab.reflected.secure') }}" class="nav-link text-success">
                                <i class="bi bi-lock"></i> Reflected (Secure)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('xss-lab.stored.vulnerable') }}" class="nav-link text-danger">
                                <i class="bi bi-unlock"></i> Stored (Vuln)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('xss-lab.stored.secure') }}" class="nav-link text-success">
                                <i class="bi bi-lock"></i> Stored (Secure)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('xss-lab.dom.vulnerable') }}" class="nav-link text-danger">
                                <i class="bi bi-unlock"></i> DOM-Based (Vuln)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('xss-lab.dom.secure') }}" class="nav-link text-success">
                                <i class="bi bi-lock"></i> DOM-Based (Secure)
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Validation Lab --}}
                <li class="nav-item">
                    <a href="#validationLab" class="nav-link {{ request()->routeIs('validation-lab.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('validation-lab.*') ? 'true' : 'false' }}">
                        <i class="bi bi-check-circle"></i> Input Validation
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('validation-lab.*') ? 'show' : '' }}" id="validationLab">
                        <li class="nav-item">
                            <a href="{{ route('validation-lab.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('validation-lab.vulnerable') }}" class="nav-link text-danger">
                                <i class="bi bi-unlock"></i> Vulnerable Form
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('validation-lab.secure') }}" class="nav-link text-success">
                                <i class="bi bi-lock"></i> Secure Form
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- CSRF Lab --}}
                <li class="nav-item">
                    <a href="#csrfLab" class="nav-link {{ request()->routeIs('csrf-lab.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('csrf-lab.*') ? 'true' : 'false' }}">
                        <i class="bi bi-key"></i> CSRF Lab
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('csrf-lab.*') ? 'show' : '' }}" id="csrfLab">
                        <li class="nav-item">
                            <a href="{{ route('csrf-lab.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('csrf-lab.how-it-works') }}" class="nav-link text-info">
                                <i class="bi bi-lightbulb"></i> How It Works
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('csrf-lab.attack-demo') }}" class="nav-link text-danger">
                                <i class="bi bi-bug"></i> Attack Demo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('csrf-lab.protection-demo') }}" class="nav-link text-success">
                                <i class="bi bi-shield-check"></i> Protection Demo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('csrf-lab.ajax-demo') }}" class="nav-link text-warning">
                                <i class="bi bi-lightning"></i> AJAX Demo
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- SQLi Lab --}}
                <li class="nav-item">
                    <a href="#sqliLab" class="nav-link {{ request()->routeIs('sqli-lab.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('sqli-lab.*') ? 'true' : 'false' }}">
                        <i class="bi bi-database-exclamation"></i> SQLi Lab
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('sqli-lab.*') ? 'show' : '' }}" id="sqliLab">
                        <li class="nav-item">
                            <a href="{{ route('sqli-lab.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sqli-lab.how-it-works') }}" class="nav-link text-info">
                                <i class="bi bi-lightbulb"></i> How It Works
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sqli-lab.vulnerable-search') }}" class="nav-link text-danger">
                                <i class="bi bi-search"></i> Vulnerable Search
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sqli-lab.vulnerable-login') }}" class="nav-link text-danger">
                                <i class="bi bi-key"></i> Login Bypass
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sqli-lab.secure-search') }}" class="nav-link text-success">
                                <i class="bi bi-shield-check"></i> Secure Search
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sqli-lab.cheatsheet') }}" class="nav-link">
                                <i class="bi bi-journal-code"></i> Cheatsheet
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Auth Lab --}}
                <li class="nav-item">
                    <a href="#authLab" class="nav-link {{ request()->routeIs('auth-lab.*') || request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('vulnerable.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('auth-lab.*') || request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('vulnerable.*') ? 'true' : 'false' }}">
                        <i class="bi bi-shield-lock"></i> Auth Lab
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('auth-lab.*') || request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('vulnerable.*') ? 'show' : '' }}" id="authLab">
                        <li class="nav-item">
                            <a href="{{ route('auth-lab.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('auth-lab.comparison') }}" class="nav-link text-info">
                                <i class="bi bi-arrows-angle-expand"></i> Comparison
                            </a>
                        </li>
                        {{-- Secure Auth --}}
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link text-success">
                                <i class="bi bi-box-arrow-in-right"></i> Login (Secure)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="nav-link text-success">
                                <i class="bi bi-person-plus"></i> Register (Secure)
                            </a>
                        </li>
                        {{-- Vulnerable Auth --}}
                        <li class="nav-item">
                            <a href="{{ route('vulnerable.login') }}" class="nav-link text-danger">
                                <i class="bi bi-unlock"></i> Login (Vulnerable)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vulnerable.register') }}" class="nav-link text-danger">
                                <i class="bi bi-person-plus-fill"></i> Register (Vulnerable)
                            </a>
                        </li>
                        {{-- Demo/Debug --}}
                        <li class="nav-item">
                            <a href="{{ route('vulnerable.show-users') }}" class="nav-link text-warning">
                                <i class="bi bi-database"></i> Show DB Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vulnerable.brute-force-stats') }}" class="nav-link text-warning">
                                <i class="bi bi-graph-up"></i> Brute Force Stats
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Authorization (RBAC) - Minggu 4 Hari 2 --}}
                <li class="nav-item">
                    <a href="#authorizationLab" class="nav-link {{ request()->routeIs('authorization-lab.*') || request()->routeIs('admin.*') || request()->routeIs('dashboard') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('authorization-lab.*') || request()->routeIs('admin.*') || request()->routeIs('dashboard') ? 'true' : 'false' }}">
                        <i class="bi bi-person-badge"></i> Authorization
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('authorization-lab.*') || request()->routeIs('admin.*') || request()->routeIs('dashboard') ? 'show' : '' }}" id="authorizationLab">
                        {{-- Lab Pages --}}
                        <li class="nav-item">
                            <a href="{{ route('authorization-lab.index') }}" class="nav-link {{ request()->routeIs('authorization-lab.index') ? 'active' : '' }}">
                                <i class="bi bi-house"></i> Lab Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('authorization-lab.login') }}" class="nav-link {{ request()->routeIs('authorization-lab.login') ? 'active' : '' }}">
                                <i class="bi bi-box-arrow-in-right"></i> Test Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('authorization-lab.implementation') }}" class="nav-link {{ request()->routeIs('authorization-lab.implementation') ? 'active' : '' }}">
                                <i class="bi bi-code-slash"></i> Implementation
                            </a>
                        </li>
                        <hr class="my-1 mx-3">
                        @auth
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            @can('access-admin')
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link text-danger {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="bi bi-gear"></i> Admin Panel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.users') }}" class="nav-link text-danger {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                    <i class="bi bi-people"></i> Manage Users
                                </a>
                            </li>
                            @endcan
                            @can('view-reports')
                            <li class="nav-item">
                                <a href="{{ route('admin.reports') }}" class="nav-link text-info {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                                    <i class="bi bi-graph-up-arrow"></i> Reports
                                </a>
                            </li>
                            @endcan
                        @else
                            <li class="nav-item">
                                <a href="{{ route('authorization-lab.login') }}" class="nav-link text-muted">
                                    <i class="bi bi-lock"></i> Login untuk Demo
                                </a>
                            </li>
                        @endauth
                    </ul>
                </li>

                {{-- BAC/IDOR Lab - Minggu 4 Hari 4 --}}
                <li class="nav-item">
                    <a href="#bacLab" class="nav-link {{ request()->routeIs('bac-lab.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('bac-lab.*') ? 'true' : 'false' }}">
                        <i class="bi bi-shield-exclamation"></i> BAC/IDOR Lab
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('bac-lab.*') ? 'show' : '' }}" id="bacLab">
                        <li class="nav-item">
                            <a href="{{ route('bac-lab.home') }}" class="nav-link {{ request()->routeIs('bac-lab.home') ? 'active' : '' }}">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            @auth
                                <a href="{{ route('bac-lab.vulnerable.tickets.index') }}" class="nav-link text-danger {{ request()->routeIs('bac-lab.vulnerable.tickets.*') ? 'active' : '' }}">
                                    <i class="bi bi-unlock"></i> Vulnerable (IDOR)
                                </a>
                            @else
                                <a href="{{ route('bac-lab.vulnerable.login') }}" class="nav-link text-danger {{ request()->routeIs('bac-lab.vulnerable.login') ? 'active' : '' }}">
                                    <i class="bi bi-unlock"></i> Vulnerable (IDOR)
                                </a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            @auth
                                <a href="{{ route('bac-lab.secure.tickets.index') }}" class="nav-link text-success {{ request()->routeIs('bac-lab.secure.tickets.*') ? 'active' : '' }}">
                                    <i class="bi bi-lock"></i> Secure (Policy)
                                </a>
                            @else
                                <a href="{{ route('bac-lab.secure.login') }}" class="nav-link text-success {{ request()->routeIs('bac-lab.secure.login') ? 'active' : '' }}">
                                    <i class="bi bi-lock"></i> Secure (Policy)
                                </a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('bac-lab.comparison') }}" class="nav-link {{ request()->routeIs('bac-lab.comparison') ? 'active' : '' }}">
                                <i class="bi bi-arrows-angle-expand"></i> Comparison
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- File Upload Lab - Minggu 5 Hari 3 & 4 --}}
                <li class="nav-item">
                    <a href="#fileUploadLab" class="nav-link {{ request()->routeIs('file-upload-lab.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('file-upload-lab.*') ? 'true' : 'false' }}">
                        <i class="bi bi-cloud-upload"></i> File Upload Lab
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('file-upload-lab.*') ? 'show' : '' }}" id="fileUploadLab">
                        <li class="nav-item">
                            <a href="{{ route('file-upload-lab.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('file-upload-lab.overview', 'logging') }}" class="nav-link text-info">
                                <i class="bi bi-journal-text"></i> Logging Materi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('file-upload-lab.overview', 'upload-basics') }}" class="nav-link text-info">
                                <i class="bi bi-book"></i> Upload Basics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('file-upload-lab.vulnerable.index') }}" class="nav-link text-danger">
                                <i class="bi bi-bug"></i> Vulnerable Lab
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('file-upload-lab.secure.index') }}" class="nav-link text-success">
                                <i class="bi bi-shield-check"></i> Secure Upload
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- TOOLS --}}
                <li class="nav-section">Tools</li>

                {{-- Security Testing --}}
                <li class="nav-item">
                    <a href="#securityTesting" class="nav-link {{ request()->routeIs('security-testing.*') ? '' : 'collapsed' }}"
                       data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('security-testing.*') ? 'true' : 'false' }}">
                        <i class="bi bi-shield-shaded"></i> Security Testing
                    </a>
                    <ul class="collapse nav-collapse {{ request()->routeIs('security-testing.*') ? 'show' : '' }}" id="securityTesting">
                        <li class="nav-item">
                            <a href="{{ route('security-testing.index') }}" class="nav-link">
                                <i class="bi bi-house"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('security-testing.xss') }}" class="nav-link">
                                <i class="bi bi-shield-exclamation"></i> XSS Test
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('security-testing.csrf') }}" class="nav-link">
                                <i class="bi bi-key"></i> CSRF Test
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('security-testing.headers') }}" class="nav-link">
                                <i class="bi bi-server"></i> Headers Test
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('security-testing.audit') }}" class="nav-link">
                                <i class="bi bi-clipboard-check"></i> Audit Checklist
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>

        {{-- Sidebar Footer --}}
        <div class="sidebar-footer">
            <div class="text-center">
                <small>Bootcamp Secure Coding</small><br>
                <small>SMK Wikrama Bogor</small>
            </div>
        </div>
    </aside>

    {{-- Sidebar Overlay (Mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ============================================ --}}
    {{-- MAIN WRAPPER --}}
    {{-- ============================================ --}}
    <div class="main-wrapper" id="mainWrapper">
        {{-- Top Bar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}"><i class="bi bi-house"></i></a>
                            </li>
                            @hasSection('breadcrumb')
                                @yield('breadcrumb')
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="topbar-right">
                {{-- SQLi Lab Quick Actions --}}
                @if(request()->routeIs('sqli-lab.*'))
                <a href="{{ route('sqli-lab.seed') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Seed
                </a>
                <a href="{{ route('sqli-lab.reset') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
                @endif

                {{-- User Status & Actions --}}
                <div class="d-flex align-items-center gap-2">
                    @auth
                        {{-- Secure Auth User --}}
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success" title="Dashboard">
                            <i class="bi bi-speedometer2"></i>
                        </a>
                        <span class="badge bg-success">
                            <i class="bi bi-person-check"></i> {{ Auth::user()->name }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Logout (Secure)">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    @endauth

                    @if(session('vulnerable_user'))
                        {{-- Vulnerable Auth User --}}
                        <a href="{{ route('vulnerable.dashboard') }}" class="btn btn-sm btn-danger" title="Dashboard (Vulnerable)">
                            <i class="bi bi-speedometer2"></i>
                        </a>
                        <span class="badge bg-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ session('vulnerable_user')->name ?? 'Vulnerable User' }}
                        </span>
                        <form method="POST" action="{{ route('vulnerable.logout') }}" class="d-inline">
                            @csrf
                            <a href="{{ route('vulnerable.logout') }}" class="btn btn-sm btn-outline-danger" title="Logout (Vulnerable)"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                            </a>
                        </form>
                    @endif

                    @guest
                        @if(!session('vulnerable_user'))
                            {{-- Not logged in --}}
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i> Guest
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><h6 class="dropdown-header"><i class="bi bi-shield-check"></i> Secure Auth</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right text-success"></i> Login</a></li>
                                    <li><a class="dropdown-item" href="{{ route('register') }}"><i class="bi bi-person-plus text-success"></i> Register</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header"><i class="bi bi-exclamation-triangle"></i> Vulnerable Auth</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('vulnerable.login') }}"><i class="bi bi-unlock text-danger"></i> Login</a></li>
                                    <li><a class="dropdown-item" href="{{ route('vulnerable.register') }}"><i class="bi bi-person-plus-fill text-danger"></i> Register</a></li>
                                </ul>
                            </div>
                        @endif
                    @endguest
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="main-content">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('danger') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page Content --}}
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="main-footer">
            <p class="mb-1">
                <i class="bi bi-shield-lock"></i> Secure Ticketing System - Bootcamp Secure Coding
            </p>
            <p class="mt-2 mb-0 text-muted small">
                &copy; {{ date('Y') }} SMK Wikrama Bogor
            </p>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Sidebar Toggle Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Sync classes with localStorage state (for elements that need JS classes)
            if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth >= 992) {
                sidebar.classList.add('collapsed');
                mainWrapper.classList.add('expanded');
            }

            // Remove initial load class and enable transitions after a brief moment
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    document.documentElement.classList.remove('sidebar-collapsed-on-load');
                    document.documentElement.classList.add('sidebar-ready');
                });
            });

            // Toggle sidebar (works on both mobile and desktop)
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth >= 992) {
                    // Desktop: toggle collapsed state
                    sidebar.classList.toggle('collapsed');
                    mainWrapper.classList.toggle('expanded');
                    // Save state
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                } else {
                    // Mobile: show sidebar overlay
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                }
            });

            // Close sidebar button (mobile & desktop collapsed toggle)
            sidebarClose.addEventListener('click', function() {
                if (window.innerWidth >= 992) {
                    // Desktop: collapse sidebar
                    sidebar.classList.add('collapsed');
                    mainWrapper.classList.add('expanded');
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    // Mobile: hide sidebar
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });

            // Close sidebar when clicking overlay (mobile only)
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    // Desktop: remove mobile show class, apply saved state
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    if (localStorage.getItem('sidebarCollapsed') === 'true') {
                        sidebar.classList.add('collapsed');
                        mainWrapper.classList.add('expanded');
                    }
                } else {
                    // Mobile: remove collapsed state
                    sidebar.classList.remove('collapsed');
                    mainWrapper.classList.remove('expanded');
                }
            });
        });
    </script>

    {{-- Global CSRF Setup untuk AJAX --}}
    <script>
        // Setup CSRF token untuk semua fetch requests
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Helper function untuk fetch dengan CSRF
        window.secureFetch = function(url, options = {}) {
            options.headers = {
                ...options.headers,
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            };
            return fetch(url, options);
        };
    </script>

    {{-- Stack untuk JavaScript tambahan per halaman --}}
    @stack('scripts')
</body>

</html>
