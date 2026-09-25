<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Homax Homes</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <!-- Google Fonts -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.1/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css">
    <!-- Brand palette: must stay last so it overrides AdminLTE + plugin defaults -->
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}">
    @yield('extraCss')
    <style>
        body,
        .main-header,
        .main-sidebar,
        .content-wrapper,
        .btn,
        .form-control,
        .nav-link {
            font-family: "Mulish", sans-serif;
        }

        h1,
        h2,
        h3,
        .brand-text {
            font-family: "Mulish", sans-serif;
            font-weight: 400;
        }

        /* Collapsed (desktop) sidebar: icons only, no logo/search/text overflow */
        .sidebar-mini.sidebar-collapse .main-sidebar {
            overflow: hidden;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .brand-link img,
        .sidebar-mini.sidebar-collapse .main-sidebar .form-inline,
        .sidebar-mini.sidebar-collapse .main-sidebar .nav-sidebar .nav-link p {
            display: none !important;
        }
    </style>
    @include('includes.fonts')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.logout') }}" role="button"> <i
                            class="fas fa-sign-out-alt"></i>
                    </a>

                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
            <!-- Brand Logo -->
            {{-- Light variant: sidebar background is brand navy #000033. --}}
            <a href="{{ route('admin.dashboard') }}" class="brand-link text-center">
                <img src="{{ asset('assets/logo/homax-logo-light.png') }}" alt="Homax Homes" width="480" height="160"
                    style="height:40px;width:auto;display:inline-block;opacity:1;">
            </a>
            <!-- SidebarSearch Form -->
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                        aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">

                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    @php
                    $admin = Auth::guard('admin')->user();
                    @endphp



                    @if ($admin?->hasPermission('all_property'))
                    <li class="nav-item">
                        <a href="{{ route('admin.properties.list') }}"
                            class="nav-link {{ Request::is('admin/properties') ? 'active' : '' }}">
                            <i class="fas fa-list nav-icon"></i>
                            <p>All Properties</p>
                        </a>
                    </li>
                    @endif


                    @if ($admin?->hasPermission('featured_image'))
                    <li class="nav-item">
                        <a href="{{ route('admin.properties.indexfeatured') }}"
                            class="nav-link {{ Request::is('admin/propertiesfeatured') ? 'active' : '' }}">
                            <i class="fas fa-list nav-icon"></i>
                            <p>Featured Properties</p>
                        </a>
                    </li>
                    @endif

                    @if ($admin?->hasPermission('add_now'))
                    <li class="nav-item">
                        <a href="{{ route('admin.propertylisting') }}"
                            class="nav-link {{ Request::is('admin/propertylisting') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle nav-icon"></i>
                            <p>Add New Property</p>
                        </a>
                    </li>
                    @endif

                    @if ($admin?->hasPermission('property_image'))
                    <li class="nav-item">
                        <a href="{{ route('admin.inquiryformlist') }}"
                            class="nav-link {{ Request::is('admin/enquiryformlist') || Request::is('admin/inquiryformlist') ? 'active' : '' }}">
                            <i class="fas fa-envelope-open-text nav-icon"></i>
                            <p>Property Enquiry</p>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.settings.edit') }}"
                            class="nav-link {{ Request::is('admin/site-settings') ? 'active' : '' }}">
                            <i class="fas fa-image nav-icon"></i>
                            <p>Site Settings</p>
                        </a>
                    </li>

                    @if ($admin?->hasPermission('our_team'))
                    <li class="nav-item">
                        <a href="{{ route('our_team.index') }}"
                            class="nav-link {{ Request::is('admin/our_team*') ? 'active' : '' }}">
                            <i class="fas fa-users nav-icon"></i>
                            <p>Our Team</p>
                        </a>
                    </li>
                    @endif

                    @if ($admin?->hasPermission('manage_users'))
                    <li class="nav-item">
                        <a href="{{ route('user_permission.index') }}"
                            class="nav-link {{ Request::is('admin/user-permission*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield nav-icon"></i>
                            <p>User Permission</p>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.logout') }}" class="nav-link">
                            <i class="fas fa-sign-out-alt nav-icon"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                    <!-- Add other nav-items similarly -->
                </ul>
            </nav>

            <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->

    @yield('content')
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="/">Homax Homes</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.2.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
    <!-- JQVMap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.1/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    @yield('extraJs')
    @yield('scripts')
</body>

</html>