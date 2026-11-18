<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    @include('admin.layouts.includes.header')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="text-end px-3 py-2"><a href="{{ route('language.switch', ['locale' => 'ar']) }}">@lang('site.arabic')</a> | <a href="{{ route('language.switch', ['locale' => 'en']) }}">@lang('site.english')</a></div>
    <div class="wrapper">
        <!-- Navbar -->
        @include('receptionist.layouts.navebar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('receptionist.layouts.sidebar')

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
