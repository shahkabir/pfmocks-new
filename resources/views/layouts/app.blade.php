<!-- Use this layout file for all views-->
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  @include('layouts.header')
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body navbar-orange navbar-light">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          @include('layouts.nav-top')
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      @include('layouts.menu')
      <!--end::Sidebar-->
      <!--begin::App Main-->
      @yield('content')
      <!--end::App Main-->
      <!--begin::Footer-->
      @include('layouts.footer')
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    @include('layouts.scripts')
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
