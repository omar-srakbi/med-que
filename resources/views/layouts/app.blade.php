<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medical Center System')</title>
    
    <!-- Bootstrap 5 RTL/LTR -->
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    @endif
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link i {
            margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 0.5rem;
        }
        
        .sidebar-brand {
            color: #fff;
            font-size: 1.25rem;
            font-weight: bold;
            padding: 1.5rem 1rem;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .main-content {
            padding: 1.5rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        
        .stat-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card.success { border-left-color: var(--success-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @auth
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <i class="bi bi-hospital"></i>
                    {{ app()->getLocale() === 'ar' ? 'نظام المركز الطبي' : 'Medical Center System' }}
                </a>
                
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            {{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}
                        </a>
                    </li>
                    
                    @if(auth()->user()->hasPermission('view_patients'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                            <i class="bi bi-people"></i>
                            {{ app()->getLocale() === 'ar' ? 'المرضى' : 'Patients' }}
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->hasPermission('create_tickets') || auth()->user()->hasPermission('create_advance_tickets'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                            <i class="bi bi-ticket-perforated"></i>
                            {{ app()->getLocale() === 'ar' ? 'التذاكر' : 'Tickets' }}
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->hasPermission('create_payments'))
                    {{-- Payments - to be implemented --}}
                    {{-- @can('create_payments')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                            <i class="bi bi-cash-stack"></i>
                            {{ app()->getLocale() === 'ar' ? 'المدفوعات' : 'Payments' }}
                        </a>
                    </li>
                    @endcan --}}
                    @endif
                    
                    @if(auth()->user()->hasPermission('view_medical_records'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}" href="{{ route('medical-records.index') }}">
                            <i class="bi bi-file-earmark-medical"></i>
                            {{ app()->getLocale() === 'ar' ? 'السجلات الطبية' : 'Medical Records' }}
                        </a>
                    </li>
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                            <i class="bi bi-graph-up"></i>
                            {{ app()->getLocale() === 'ar' ? 'التقارير' : 'Reports' }}
                        </a>
                    </li>
                    
                    @if(auth()->user()->hasPermission('manage_settings'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                            <i class="bi bi-gear"></i>
                            {{ app()->getLocale() === 'ar' ? 'الإعدادات' : 'Settings' }}
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->role->name === 'Admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                            <i class="bi bi-shield-check"></i>
                            {{ app()->getLocale() === 'ar' ? 'سجل التدقيق' : 'Audit Logs' }}
                        </a>
                    </li>
                    @endif
                    
                    {{-- Staff, Roles, Departments --}}
                    @can('*')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                            <i class="bi bi-person-badge"></i>
                            {{ app()->getLocale() === 'ar' ? 'الموظفين' : 'Staff' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                            <i class="bi bi-shield-lock"></i>
                            {{ app()->getLocale() === 'ar' ? 'الأدوار' : 'Roles' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                            <i class="bi bi-building"></i>
                            {{ app()->getLocale() === 'ar' ? 'الأقسام' : 'Departments' }}
                        </a>
                    </li>
                    @endcan
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('queue.display') }}">
                            <i class="bi bi-display"></i>
                            {{ app()->getLocale() === 'ar' ? 'عرض الطابور' : 'Queue Display' }}
                        </a>
                    </li>
                    
                    <li class="nav-item mt-4">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                                <i class="bi bi-box-arrow-right"></i>
                                {{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
            @endauth
            
            <!-- Main Content -->
            <main class="{{ auth()->check() ? 'col-md-9 col-lg-10 ms-sm-auto px-md-4' : '' }} main-content">
                @auth
                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h4 class="mb-0">@yield('page-title')</h4>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Global Search -->
                        <div class="position-relative" style="min-width: 300px;">
                            <input type="text" id="global-search" class="form-control" 
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'بحث...' : 'Search...' }}" 
                                   autocomplete="off">
                            <div id="search-results" class="position-absolute w-100 bg-white border shadow-lg rounded mt-1" 
                                 style="display: none; max-height: 400px; overflow-y: auto; z-index: 1000;"></div>
                        </div>
                        
                        <!-- Language Switcher -->
                        <form action="{{ route('language.switch') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-globe"></i>
                                {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                            </button>
                        </form>
                        
                        <!-- User Info -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                {{ auth()->user()->full_name }}
                                <span class="badge bg-secondary">{{ auth()->user()->role->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text text-muted">{{ auth()->user()->email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endauth
                
                <!-- Alerts -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Global Search
    const searchInput = document.getElementById('global-search');
    const searchResults = document.getElementById('search-results');
    let searchTimeout = null;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`{{ route('search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    });
            }, 300);
        });
        
        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
    
    function displaySearchResults(data) {
        let html = '';
        let hasResults = false;
        
        if (data.patients && data.patients.length > 0) {
            hasResults = true;
            html += '<div class="search-section"><h6 class="search-section-title">👥 ' + (document.documentElement.lang === 'ar' ? 'المرضى' : 'Patients') + '</h6>';
            data.patients.forEach(item => {
                html += `<a href="${item.url}" class="search-item"><div><strong>${item.name}</strong></div><small class="text-muted">${item.national_id}</small></a>`;
            });
            html += '</div>';
        }
        
        if (data.tickets && data.tickets.length > 0) {
            hasResults = true;
            html += '<div class="search-section"><h6 class="search-section-title">🎫 ' + (document.documentElement.lang === 'ar' ? 'التذاكر' : 'Tickets') + '</h6>';
            data.tickets.forEach(item => {
                html += `<a href="${item.url}" class="search-item"><div><strong>${item.ticket_number}</strong> - ${item.patient}</div><small class="text-muted">${item.department}</small></a>`;
            });
            html += '</div>';
        }
        
        if (data.medical_records && data.medical_records.length > 0) {
            hasResults = true;
            html += '<div class="search-section"><h6 class="search-section-title">📋 ' + (document.documentElement.lang === 'ar' ? 'السجلات الطبية' : 'Medical Records') + '</h6>';
            data.medical_records.forEach(item => {
                html += `<a href="${item.url}" class="search-item"><div><strong>${item.patient}</strong></div><small class="text-muted">${item.diagnosis || ''}</small></a>`;
            });
            html += '</div>';
        }
        
        if (data.staff && data.staff.length > 0) {
            hasResults = true;
            html += '<div class="search-section"><h6 class="search-section-title">👤 ' + (document.documentElement.lang === 'ar' ? 'الموظفين' : 'Staff') + '</h6>';
            data.staff.forEach(item => {
                html += `<a href="${item.url}" class="search-item"><div><strong>${item.name}</strong></div><small class="text-muted">${item.role}</small></a>`;
            });
            html += '</div>';
        }
        
        if (!hasResults) {
            html = '<div class="search-item text-muted text-center py-3">' + (document.documentElement.lang === 'ar' ? 'لا توجد نتائج' : 'No results found') + '</div>';
        }
        
        searchResults.innerHTML = html;
        searchResults.style.display = hasResults ? 'block' : 'none';
    }
    </script>
    
    <style>
    .search-section { padding: 0.5rem 0; }
    .search-section:first-child { border-top: none; }
    .search-section-title {
        padding: 0.5rem 1rem;
        margin: 0;
        background: #f8f9fa;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6c757d;
    }
    .search-item {
        display: block;
        padding: 0.5rem 1rem;
        color: #212529;
        text-decoration: none;
        border-bottom: 1px solid #eee;
    }
    .search-item:hover {
        background: #f8f9fa;
        text-decoration: none;
    }
    .search-item:last-child { border-bottom: none; }
    </style>

    @stack('scripts')
</body>
</html>
