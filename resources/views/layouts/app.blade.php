<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png">
  <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    @yield('title', 'Monitoring Power')
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    body, h1, h2, h3, h4, h5, h6, .card-category, .title, .stats, .copyright, .navbar-brand, .nav p, .table {
      font-family: 'Inter', sans-serif !important;
    }
    /* Smooth Sidebar Interaction */
    .sidebar .nav li > a {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .sidebar .nav li:hover > a {
        transform: translateX(8px);
        background-color: rgba(255, 255, 255, 0.1);
    }
    .sidebar-wrapper {
        overflow-x: hidden; /* Prevent scrollbar during slide */
    }
    
    /* GLOBAL CARD HOVER EFFECT (Premium Floating) */
    .card {
        transition: transform 0.3s cubic-bezier(0.34, 1.61, 0.7, 1), box-shadow 0.3s ease !important;
    }
    .card:hover {
        transform: translateY(-7px) !important;
        box-shadow: 0 15px 35px rgba(50, 50, 93, 0.15), 0 5px 15px rgba(0, 0, 0, 0.1) !important;
        z-index: 10; /* Bring to front on hover */
    }
  </style>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <!-- CSS Files -->
  <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" />
  <link href="{{ asset('assets') }}/css/now-ui-dashboard.css?v=1.5.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ asset('assets') }}/demo/demo.css" rel="stylesheet" />
  @stack('styles')
</head>

<body class="">
  <script>
      // PREVENT WHITE FLASH: Check theme immediately before content renders
      if (localStorage.getItem('theme') === 'dark') {
          document.body.classList.add('dark-mode');
      }
  </script>
  <div class="wrapper ">
    <div class="sidebar" data-color="orange">
      <div class="logo" style="padding: 10px 20px; text-align: center;">
        <a href="{{ route('dashboard') }}" class="simple-text logo-normal" style="margin: 0; display: block;">
          <img src="{{ asset('assets/img/logo-monitoring-power.svg') }}" alt="Monitoring Power" style="max-width: 210px;">
        </a>
      </div>
      <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
          <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
              <i class="now-ui-icons design_app"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="{{ request()->routeIs('history') ? 'active' : '' }}">
            <a href="{{ route('history') }}">
              <i class="now-ui-icons design_bullet-list-67"></i>
              <p>History & Logs</p>
            </a>
          </li>
          <li class="{{ request()->routeIs('alarms') ? 'active' : '' }}">
            <a href="{{ route('alarms') }}">
              <i class="now-ui-icons ui-1_bell-53"></i>
              <p>Alarms & Notifications</p>
            </a>
          </li>
          <li class="{{ request()->routeIs('analytics') ? 'active' : '' }}">
            <a href="{{ route('analytics') }}">
              <i class="now-ui-icons business_chart-bar-32"></i>
              <p>Energy Analytics</p>
            </a>
          </li>
          @if(auth()->check() && auth()->user()->role === 'admin')
          <li class="{{ request()->routeIs('control') ? 'active' : '' }}">
            <a href="{{ route('control') }}">
              <i class="now-ui-icons ui-2_settings-90"></i>
              <p>Control & Automation</p>
            </a>
          </li>
          <li>
            <a data-toggle="collapse" href="#masterDataMenu" aria-expanded="{{ (request()->routeIs('master-data*') || request()->routeIs('power-sources*')) ? 'true' : 'false' }}">
              <i class="now-ui-icons files_box"></i>
              <p>
                Master Data
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse {{ (request()->routeIs('master-data*') || request()->routeIs('power-sources*')) ? 'show' : '' }}" id="masterDataMenu">
              <ul class="nav">
                <li class="{{ request()->routeIs('master-data*') ? 'active' : '' }}" style="margin-left: 30px; border-left: 1px dashed rgba(255,255,255,0.3); padding-left: 15px;">
                  <a href="{{ route('master-data.index') }}">
                    <span class="sidebar-mini-icon"><i class="now-ui-icons business_bank"></i></span>
                    <span class="sidebar-normal">Data Gedung</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('power-sources*') ? 'active' : '' }}" style="margin-left: 30px; border-left: 1px dashed rgba(255,255,255,0.3); padding-left: 15px;">
                  <a href="{{ route('power-sources.index') }}">
                    <span class="sidebar-mini-icon">
                      <i class="fas fa-bolt"></i>
                    </span>
                    <span class="sidebar-normal"> Power Sources </span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="{{ request()->routeIs('users*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}">
              <i class="now-ui-icons users_single-02"></i>
              <p>User Management</p>
            </a>
          </li>
          @endif
        </ul>
      </div>
    </div>
    <div class="main-panel" id="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute navbar-light bg-white" style="box-shadow: 0 2px 15px rgba(0,0,0,0.1);">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand d-flex align-items-center" href="#pablo" style="font-weight: 800; font-size: 1.2rem;">
              <i class="@yield('page-icon', 'now-ui-icons design_app') mr-2 text-warning" style="font-size: 1.2rem; font-weight: 800;"></i>
              @yield('page-title', 'DASHBOARD')
            </a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              <li class="nav-item d-flex align-items-center">
                  <div class="dm-toggle-wrapper mt-2 mr-2">
                       <i class="fas fa-sun mr-2 text-warning" id="dm-icon" style="font-size: 1.2rem; transition: all 0.3s ease;"></i>
                       <label class="dm-switch" for="darkmode-toggle" style="margin-bottom: 0;">
                            <input type="checkbox" id="darkmode-toggle" onchange="toggleDarkMode()">
                            <span class="dm-slider round"></span>
                       </label>
                  </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                  <i class="now-ui-icons users_single-02"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Account</span>
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink" style="border-radius: 10px;">
                     <span class="dropdown-item text-muted disabled">Signed in as {{ auth()->user()->name ?? 'User' }}</span>
                     <div class="dropdown-divider"></div>
                     <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="now-ui-icons media-1_button-power mr-2"></i> Logout
                     </a>
                     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                     </form>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      
      @yield('content')

      <footer class="footer">
        <div class=" container-fluid ">
          <div class="copyright" id="copyright">
            &copy; <script>
              document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))
            </script>, Power Monitoring System
          </div>
        </div>
      </footer>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="{{ asset('assets') }}/js/core/jquery.min.js"></script>
  <script src="{{ asset('assets') }}/js/core/popper.min.js"></script>
  <script src="{{ asset('assets') }}/js/core/bootstrap.min.js"></script>
  <script src="{{ asset('assets') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!-- Chart JS -->
  <script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="{{ asset('assets') }}/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('assets') }}/js/now-ui-dashboard.min.js?v=1.5.0" type="text/javascript"></script>
  @stack('scripts')
  
  <style>
    /* Smooth Theme Transition for ALL major elements */
    body, .wrapper, .sidebar, .main-panel, .navbar, .card, .card-header, .card-body, .table, .modal-content, .form-control, .dropdown-menu, h1, h2, h3, h4, h5, h6, p, span, i {
        transition: background-color 0.5s ease, color 0.5s ease, border-color 0.5s ease, box-shadow 0.5s ease !important;
    }

    /* Smooth Table Hover */
    .table tbody tr {
        transition: background-color 0.3s ease !important;
    }
    .table tbody tr:hover {
        transition: background-color 0.1s ease !important; /* Slightly faster on hover in for responsiveness, but smooth out */
    }

    /* Dark Mode Variables */
    body.dark-mode, 
    body.dark-mode .wrapper, 
    body.dark-mode .main-panel, 
    body.dark-mode .content,
    body.dark-mode .panel-header {
        background-color: #1a1e34 !important; /* Deep Navy Background */
        color: #e2e2e2 !important;
    }
    
    /* --- NEW SCOPED DARK MODE TOGGLE STYLES --- */
    .dm-toggle-wrapper {
        display: flex;
        align-items: center;
    }
    .dm-switch {
        position: relative;
        display: inline-block;
        width: 54px;
        height: 26px;
    }
    .dm-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .dm-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #e4e4e4;
        transition: .4s;
        border-radius: 34px;
        border: 1px solid #ddd;
    }
    .dm-knob {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 4px; bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        z-index: 2;
    }

    /* Checked State (Dark Mode) */
    .dm-switch input:checked + .dm-slider {
        background-color: #32325d; /* Darker track */
        border-color: #32325d;
    }
    .dm-switch input:checked + .dm-slider:before {
        transform: translateX(26px);
    }
    
    /* Move knob (using pseudo element or span, wait, I removed the span knob in HTML) */
    /* Wait, I removed the inner span knob in HTML above, so I should revert CSS to use :before on .dm-slider like original standard switch for simplicity */
    
    .dm-slider:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 4px; bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        z-index: 2;
    }
    .dm-switch input:checked + .dm-slider:before {
        transform: translateX(26px);
    }
    
    /* --- END TOGGLE STYLES --- */

    /* Sidebar Dark */
    body.dark-mode .sidebar {
        background-color: #1a1e34 !important; 
    }
    body.dark-mode .sidebar:after {
        background: #1a1e34 !important; /* Remove gradient overlay if any */
    }
    body.dark-mode .sidebar .nav li > a,
    body.dark-mode .sidebar .logo a.simple-text,
    body.dark-mode .sidebar .nav i {
        color: #ffffff !important;
    }
    body.dark-mode .sidebar .nav li.active > a {
        background-color: #f96332 !important;
        color: #ffffff !important;
    }
    
    /* Logo Invert to White */
    body.dark-mode .sidebar .logo img {
        filter: brightness(0) invert(1);
    }

    /* Navbar Dark */
    body.dark-mode .navbar {
        background-color: #1a1e34 !important;
        box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.4) !important;
    }
    body.dark-mode .navbar .navbar-brand, 
    body.dark-mode .navbar .navbar-nav .nav-link p {
        color: #ffffff !important;
    }
    body.dark-mode .navbar .navbar-toggler-bar {
        background-color: #ffffff !important;
    }
    
    /* Card Dark Mode */
    body.dark-mode .card, body.dark-mode .card-clean {
        background-color: #27293d !important; /* Lighter dark for cards */
        color: #ffffff !important;
        box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.4) !important;
    }
    body.dark-mode .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    body.dark-mode .card-title, 
    body.dark-mode .card-category, 
    body.dark-mode .stats,
    body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
        color: #ffffff !important;
    }
    body.dark-mode .text-muted {
        color: #9a9a9a !important;
    }
    
    /* Table Dark */
    body.dark-mode .table {
        color: #ffffff !important;
    }
    body.dark-mode .table thead th {
        color: #9a9a9a !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        background-color: transparent !important;
    }
    body.dark-mode .table tbody td {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    body.dark-mode .table-custom {
        background-color: transparent !important;
    }
    body.dark-mode .table-custom thead th {
         background-color: #27293d !important;
         color: #9a9a9a !important;
         border-color: rgba(255, 255, 255, 0.1) !important;
    }
    body.dark-mode .table-custom tbody tr:hover td {
         background-color: rgba(255, 255, 255, 0.05) !important;
         color: #ffffff !important;
    }
    body.dark-mode .text-dark {
        color: #ffffff !important;
    }

    /* Dropdown Dark */
    body.dark-mode .dropdown-menu {
        background-color: #27293d !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    body.dark-mode .dropdown-item {
        color: #ffffff !important;
    }
    body.dark-mode .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    /* Modal Dark */
    body.dark-mode .modal-content {
        background-color: #27293d !important;
        color: #ffffff !important;
    }
    body.dark-mode .modal-header .close span {
         color: #ffffff !important;
    }
    body.dark-mode .form-control {
        background-color: #1a1e34 !important;
        color: #ffffff !important;
        border-color: #2b3553 !important;
    }
    body.dark-mode .form-control:focus {
         background-color: #1a1e34 !important;
         border-color: #e14eca !important;
        color: #ffffff !important;
    }
    body.dark-mode .panel-header-sm {
        background: transparent !important;
    }

    /* Inputs & Labels */
    body.dark-mode label {
        color: #e2e2e2 !important;
    }
    
    /* Text Visibility Fixes */
    body.dark-mode .text-primary { color: #ffb236 !important; }   /* Lighter Orange */
    body.dark-mode .text-info { color: #63dcf9 !important; }      /* Lighter Blue */
    body.dark-mode .text-success { color: #8cf998 !important; }   /* Lighter Green */
    body.dark-mode .text-danger { color: #ff8888 !important; }    /* Lighter Red */
    body.dark-mode .text-warning { color: #ffe57f !important; }
    body.dark-mode .text-dark { color: #ffffff !important; }
    body.dark-mode .text-body { color: #e2e2e2 !important; }
    
    /* Pagination Fix */
    body.dark-mode .page-link {
        background-color: transparent !important;
        color: #e2e2e2 !important;
    }
    body.dark-mode .page-item.active .page-link {
        background-color: #f96332 !important;
        color: white !important;
    }
    body.dark-mode .page-item.disabled .page-link {
        color: #6c757d !important;
    }
    
    /* Select Dropdown Fix */
    body.dark-mode select option {
        background-color: #1a1e34;
        color: white;
    }
    
    /* Form Placeholders */
    body.dark-mode ::placeholder {
        color: #adb5bd !important;
        opacity: 1;
    }

    /* GLOBAL UTILITY OVERRIDES for Dark Mode */
    body.dark-mode .bg-white {
        background-color: transparent !important; /* Let card color show through */
        color: #ffffff !important;
    }
    body.dark-mode .bg-light {
        background-color: #1a1e34 !important; 
        color: #ffffff !important;
    }
    
    /* Specific overrides for inline styles like 'background: #f8f9fa' used in panels */
    body.dark-mode div[style*="background: #f8f9fa"], 
    body.dark-mode div[style*="background:#f8f9fa"] {
        background: #1a1e34 !important;
        color: #ffffff !important;
    }
    
    /* Input Group Text (Suffix/Prefix) */
    body.dark-mode .input-group-text {
        background-color: #1a1e34 !important; /* Darker than input */
        color: #e2e2e2 !important;
        border-color: #2b3553 !important;
    }

    /* Fix Toggle Switch in Control Panel appearing white */
    body.dark-mode .slider:before {
        background-color: #fff; /* Knob stays white */
    }

    /* HEATMAP STYLING */
    /* Light Mode (Default) Empty Slots */
    .heatmap-day.empty-day {
        background-color: #eff2f5 !important;
        color: #8898aa !important; /* Default Text Color */
    }

    /* HEATMAP DARK MODE */
    body.dark-mode .heatmap-day {
        color: #fff !important; /* Text always white */
    }
    /* Empty/Default styling override */
    body.dark-mode .heatmap-day.empty-day {
        background-color: #1a1e34 !important; /* Dark background for empty slots */
        border: 1px solid #2b3553;
        color: #adb5bd !important; /* Lighter text for visibility */
    }
  </style>

  <script>
      // 1. Check LocalStorage on Load & Set Checkbox (UI Sync Only)
      var currentTheme = localStorage.getItem('theme');
      var toggleSwitch = document.getElementById('darkmode-toggle');
      var icon = document.getElementById('dm-icon');
      
      if (currentTheme === 'dark') {
          // Class is already added by top script, just sync UI
          if(toggleSwitch) toggleSwitch.checked = true;
          if(icon) {
              icon.classList.remove('fa-sun', 'text-warning');
              icon.classList.add('fa-moon', 'text-white');
          }
      }

      function toggleDarkMode() {
          document.body.classList.toggle('dark-mode');
          var icon = document.getElementById('dm-icon');
          
          if (document.body.classList.contains('dark-mode')) {
              localStorage.setItem('theme', 'dark');
              if(icon) {
                  icon.classList.remove('fa-sun', 'text-warning');
                  icon.classList.add('fa-moon', 'text-white');
              }
          } else {
              localStorage.setItem('theme', 'light');
              if(icon) {
                  icon.classList.remove('fa-moon', 'text-white');
                  icon.classList.add('fa-sun', 'text-warning');
              }
          }

          // Force update charts/heatmap immediately if on dashboard
          if (typeof updateRealTimeData === "function") {
              updateRealTimeData();
          }
      }
  </script>
</body>

</html>
