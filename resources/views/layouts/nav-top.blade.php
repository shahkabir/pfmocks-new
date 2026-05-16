<style>
    /* ===== Top nav bar — brand gradient (#2d4797) ===== */
    .app-header.navbar {
        background: linear-gradient(135deg, #2d4797 0%, #3a5cc7 50%, #1e2f63 100%) !important;
        border-bottom: 1px solid rgba(255,255,255,.12) !important;
        box-shadow: 0 2px 8px rgba(45, 71, 151, .25);
    }
    .app-header .nav-link,
    .app-header .nav-link i {
        color: rgba(255,255,255,.92) !important;
        transition: color .15s;
    }
    .app-header .nav-link:hover,
    .app-header .nav-link:focus { color: #fff !important; }
    .app-header .nav-link:hover i { color: #fff !important; }
    .app-header .user-menu .nav-link span { color: #fff; font-weight: 500; }
    .app-header .user-menu .dropdown-toggle::after { color: rgba(255,255,255,.85); }
    .app-header .user-image { border-color: rgba(255,255,255,.7) !important; }

    /* ===== Referral discount pill — lives inside the top nav bar ===== */
    .app-header .container-fluid { position: relative; }

    .nav-referral-pill {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.30);
        color: #fff;
        font-size: .82rem;
        font-weight: 500;
        padding: 5px 16px;
        border-radius: 999px;
        white-space: nowrap;
        backdrop-filter: blur(2px);
    }
    .nav-referral-pill .gift-ico {
        color: #ffd43b;
        font-size: .95rem;
        animation: ref-gift-bounce 2.4s ease-in-out infinite;
    }
    @keyframes ref-gift-bounce {
        0%,100% { transform: translateY(0)   rotate(-6deg); }
        50%     { transform: translateY(-2px) rotate(6deg);  }
    }
    /* Only the amount flashes */
    .nav-referral-pill .ref-amount {
        font-weight: 800;
        font-size: 1rem;
        background: linear-gradient(90deg, #ffe066 0%, #ff922b 35%, #ffe066 70%, #ff922b 100%);
        background-size: 220% auto;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: ref-amount-shimmer 1.6s linear infinite,
                   ref-amount-pulse   1.4s ease-in-out infinite;
    }
    @keyframes ref-amount-shimmer {
        from { background-position: 0%   center; }
        to   { background-position: 220% center; }
    }
    @keyframes ref-amount-pulse {
        0%,100% { transform: scale(1); }
        50%     { transform: scale(1.13); }
    }
</style>

<ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            {{-- <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li> --}}
          </ul>
          <!--end::Start Navbar Links-->

          {{-- Referral discount notification — only when the logged-in user has
               a still-redeemable pending referral claim. --}}
          @auth
            @php
                $navReferral = app(\App\Services\ReferralService::class)
                                  ->pendingBannerForUser(auth()->id());
            @endphp
            @if($navReferral)
              <a href="{{ route('dashboard') }}"
                 class="nav-referral-pill d-none d-lg-inline-flex text-decoration-none"
                 title="Redeem your referral discount on your first paid purchase">
                  <i class="bi bi-gift-fill gift-ico"></i>
                  <span>Referral bonus — save</span>
                  <span class="ref-amount">{{ $navReferral['display_value'] }}</span>
                  <span>on your first purchase</span>
              </a>
            @endif
          @endauth

          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Navbar Search-->
            {{-- <li class="nav-item">
              <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="bi bi-search"></i>
              </a>
            </li> --}}
            <!--end::Navbar Search-->
            <!--begin::Messages Dropdown Menu-->
            
            <!--end::Messages Dropdown Menu-->
            <!--begin::Notifications Dropdown Menu-->
            {{-- <li class="nav-item dropdown">
              <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="bi bi-bell-fill"></i>
                <span class="navbar-badge badge text-bg-warning">15</span>
              </a>
              {{-- <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-envelope me-2"></i> 4 new messages
                  <span class="float-end text-secondary fs-7">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-people-fill me-2"></i> 8 friend requests
                  <span class="float-end text-secondary fs-7">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
                  <span class="float-end text-secondary fs-7">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
              </div> --}}
            {{-- </li> --}}
            <!--end::Notifications Dropdown Menu-->
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="{{ asset('images/profile.png') }}"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                  style="width:30px;height:30px;object-fit:cover;border:1px solid #dee2e6;"
                />
                <span class="d-none d-md-inline ms-1">{{ Auth::user()->name }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <img
                    src="{{ asset('images/profile.png') }}"
                    class="rounded-circle shadow"
                    alt="User Image"
                    style="width:80px;height:80px;object-fit:cover;border:3px solid #fff;"
                  />
                  <p>
                    {{ Auth::user()->name }}
                    <small>{{ Auth::user()->email }}</small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Body-->
                {{-- <li class="user-body">
                  <!--begin::Row-->
                  <div class="row">
                    <div class="col-4 text-center"><a href="#">Followers</a></div>
                    <div class="col-4 text-center"><a href="#">Sales</a></div>
                    <div class="col-4 text-center"><a href="#">Friends</a></div>
                  </div>
                  <!--end::Row-->
                </li> --}}
                <!--end::Menu Body-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="{{ route('profile') }}" class="btn btn-default btn-flat">Profile</a>
                  <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-end">Sign out</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>