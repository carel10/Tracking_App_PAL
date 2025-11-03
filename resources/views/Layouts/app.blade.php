<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive HTML Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="NobleUI">
    <meta name="keywords" content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <title>NobleUI - HTML Bootstrap 5 Admin Dashboard Template</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
        <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
        <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
        <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <!-- endinject -->

    <!-- Layout styles -->  
        <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
    <!-- End layout styles -->

    <!-- Custom styles for sidebar icons and active state -->
    <style>
        /* Ensure sidebar icons are visible */
        .sidebar .nav-link .link-icon {
            display: inline-block !important;
            width: 20px !important;
            height: 20px !important;
            margin-right: 10px !important;
            vertical-align: middle !important;
            min-width: 20px !important;
            min-height: 20px !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex-shrink: 0;
        }
        
        .sidebar .nav-link .link-icon svg {
            width: 20px !important;
            height: 20px !important;
            stroke-width: 2;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Ensure icons container always takes space */
        .sidebar .nav-link .link-icon[data-feather] {
            position: relative;
        }
        
        /* Make sure icon space is reserved even if not rendered */
        .sidebar .nav-link .link-icon:not(:has(svg))::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
        }
        
        /* Force icon rendering */
        .sidebar .nav-link .link-icon[data-feather] {
            color: inherit;
        }
        
        /* Active state styling */
        .sidebar .nav-link.active {
            background-color: rgba(115, 103, 240, 0.1);
            color: #7367f0 !important;
            border-left: 3px solid #7367f0;
        }
        
        .sidebar .nav-link.active .link-icon {
            color: #7367f0;
        }
        
        .sidebar .nav-link.active .link-title {
            color: #7367f0;
            font-weight: 600;
        }
        
        /* Hover effect */
        .sidebar .nav-link:hover {
            background-color: rgba(115, 103, 240, 0.05);
        }
        
        .sidebar .nav-link:hover .link-icon {
            color: #7367f0;
        }
        
        /* Enhanced Card Styling */
        .card {
            box-shadow: 0 0 1px rgba(0, 0, 0, 0.13), 0 1px 3px rgba(0, 0, 0, 0.08);
            border: none;
            transition: box-shadow 0.15s ease-in-out;
        }
        
        .card:hover {
            box-shadow: 0 0 1px rgba(0, 0, 0, 0.13), 0 2px 6px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            font-weight: 600;
        }
        
        /* Enhanced Form Styling */
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #7367f0;
            box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
        }
        
        /* Better spacing for forms */
        .card-body .form-group,
        .card-body .mb-3 {
            margin-bottom: 1.5rem;
        }
        
        /* Enhanced Table Styling */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table tbody tr {
            transition: background-color 0.15s ease-in-out;
        }
        
        .table tbody tr:hover {
            background-color: rgba(115, 103, 240, 0.03);
        }
        
        /* Better Button Spacing */
        .btn-group-sm .btn,
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        /* Enhanced Page Header */
        .grid-margin {
            margin-bottom: 1.5rem;
        }
        
        .grid-margin h4 {
            font-weight: 600;
            color: #212529;
        }
        
        .grid-margin .text-muted {
            font-size: 0.875rem;
        }
        
        /* Better Alert Styling */
        .alert {
            border: none;
            box-shadow: 0 0 1px rgba(0, 0, 0, 0.13), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        /* Enhanced Badge Styling */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        /* Better Modal Styling */
        .modal-content {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        /* Enhanced Input Group */
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        /* Better Pagination */
        .pagination .page-link {
            color: #7367f0;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #7367f0;
            border-color: #7367f0;
        }
        
        /* Consistent Spacing */
        .page-content {
            padding: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .page-content {
                padding: 1rem;
            }
        }
    </style>

        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>
<body>
    <div class="main-wrapper">
        @auth
        <!-- Sidebar -->
        @include('Layouts.sidebar')
        @endauth
        
        <div class="page-wrapper">
            @auth
            <!-- Navbar -->
            @include('Layouts.navbar')
            @endauth

            <!-- Main Content -->
            <div class="page-content">
                @yield('content')
            </div>

            @auth
            <!-- Footer -->
            @include('Layouts.footer')
            @endauth
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>

    <!-- Plugin Scripts -->
    <script src="{{ asset('assets/vendors/jquery.flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Page Specific Scripts -->
    @yield('scripts')

    <!-- Custom Scripts -->
    <script src="{{ asset('assets/js/dashboard-light.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker.js') }}"></script>
    
    <!-- Initialize feather icons and ensure sidebar functionality -->
    <script>
        (function() {
            // Wait for jQuery and feather to be available
            function waitForDependencies(callback) {
                if (typeof jQuery !== 'undefined' && typeof feather !== 'undefined') {
                    callback();
                } else {
                    setTimeout(function() {
                        waitForDependencies(callback);
                    }, 50);
                }
            }
            
            // Initialize feather icons
            function initFeatherIcons() {
                if (typeof feather !== 'undefined') {
                    // Replace all feather icons globally
                    feather.replace();
                    
                    // Force replace sidebar icons specifically
                    var sidebarIcons = document.querySelectorAll('.sidebar .link-icon[data-feather]');
                    sidebarIcons.forEach(function(icon) {
                        if (!icon.querySelector('svg')) {
                            try {
                                var iconName = icon.getAttribute('data-feather');
                                if (iconName) {
                                    feather.replace({elem: icon});
                                }
                            } catch(e) {
                                console.log('Icon replacement issue:', e);
                            }
                        }
                    });
                }
            }
            
            // Initialize everything when ready
            waitForDependencies(function() {
                // Initialize feather icons
                initFeatherIcons();
                
                // Re-initialize after short delays to ensure all icons render
                setTimeout(initFeatherIcons, 100);
                setTimeout(initFeatherIcons, 300);
                setTimeout(initFeatherIcons, 500);
                
                // Ensure sidebar toggler works
                jQuery(document).ready(function($) {
                    // Sidebar toggle functionality
                    $('.sidebar-header .sidebar-toggler').on('click', function(e) {
                        e.preventDefault();
                        $(this).toggleClass('active not-active');
                        $('body').toggleClass('sidebar-folded sidebar-open');
                    });
                    
                    // Re-initialize icons after sidebar toggle
                    $('.sidebar-header .sidebar-toggler').on('click', function() {
                        setTimeout(initFeatherIcons, 100);
                    });
                });
            });
            
            // Also initialize when DOM is ready (fallback)
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initFeatherIcons, 200);
                    setTimeout(initFeatherIcons, 500);
                });
            } else {
                setTimeout(initFeatherIcons, 200);
                setTimeout(initFeatherIcons, 500);
            }
        })();
    </script>
</body>
</html>
