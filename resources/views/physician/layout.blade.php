<!DOCTYPE html>
<html>

<head>
    @include('admin.layouts.includes.header')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('physician.layouts.navebar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('physician.layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @include('admin.layouts.includes.flash')
            @yield('contents')
        </div>
        <!-- /.content-wrapper -->
        @include('admin.layouts.includes.footer')
    </div>
    <!-- ./wrapper -->
    @include('admin.layouts.includes.scripts')
</body>

</html>
