<!DOCTYPE html>
<html>

<head>
  @include('adminlte/layout/head')
  @stack('css')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

  <div class="wrapper">

    @include('adminlte/layout/topnav')

    @include('adminlte/layout/sidenav')

    @yield('content')

    @include('adminlte/layout/footer')
  </div>

  @include('adminlte/layout/scripts')
  @stack('scripts')
</body>

</html>