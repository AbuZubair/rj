
<div class="wrapper ">
   @include('layouts.navbars.sidebar')
  <div class="main-panel">
    @include('layouts.navbars.navs.auth')
    <div class="ml-3 mt-2 d-none d-lg-flex" id="breadcrumbs">
      
    </div>
    @yield('content')
    @include('layouts.footers.auth')
  </div>
</div>