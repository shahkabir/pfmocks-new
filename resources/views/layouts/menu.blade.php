<style>
  /* Custom CSS for pulse animation on badges */
  @keyframes chip-pulse {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); /* Match badge color */
  }
  70% {
    transform: scale(1);
    box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
  }
  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
  }
}

.pulse-chip {
  animation: chip-pulse 2s infinite; /* Runs continuously */
  display: inline-block; /* Required for transform to work */
}

</style>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center justify-content-center w-100"
             style="padding:8px 12px;">
            <!--begin::Brand Image-->
            <img
              src="{{ asset('logo.png') }}"
              alt="PerfectMocks"
              style="max-width:250px;width:auto;height:auto;object-fit:contain;display:block;"
            />
            <!--end::Brand Image-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="navigation"
              aria-label="Main navigation"
              data-accordion="false"
              id="navigation"
            >
              {{-- <li class="nav-item menu-open">
                <a href="#" class="nav-link active">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Dashboard
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./index.html" class="nav-link active">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Dashboard v1</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./index2.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Dashboard v2</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./index3.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Dashboard v3</p>
                    </a>
                  </li>
                </ul>
              </li> --}}
              @if (Auth::user()->role === 'admin' || Auth::user()->role === 'user' || Auth::user()->role === 'evaluator')
                
                {{-- Evaluators will not see this menu--}}
                @if(Auth::user()->role !== 'evaluator')
                  <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                      <i class="nav-icon bi bi-openai"></i>
                      <p>My Exams</p>
                    </a>
                  </li>
                @endif
                {{-- <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-box-seam-fill"></i>
                    <p>
                      Widgets
                      <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="./widgets/small-box.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Small Box</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./widgets/info-box.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>info Box</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./widgets/cards.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Cards</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-clipboard-fill"></i>
                    <p>
                      Layout Options
                      <span class="nav-badge badge text-bg-secondary me-3">6</span>
                      <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="./layout/unfixed-sidebar.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Default Sidebar</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/fixed-sidebar.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Fixed Sidebar</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/fixed-header.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Fixed Header</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/fixed-footer.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Fixed Footer</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/fixed-complete.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Fixed Complete</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/layout-custom-area.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Layout <small>+ Custom Area </small></p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/sidebar-mini.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Sidebar Mini</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/collapsed-sidebar.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Sidebar Mini <small>+ Collapsed</small></p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/logo-switch.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Sidebar Mini <small>+ Logo Switch</small></p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./layout/layout-rtl.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Layout RTL</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-tree-fill"></i>
                    <p>
                      UI Elements
                      <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="./UI/general.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>General</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./UI/icons.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Icons</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./UI/timeline.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Timeline</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-pencil-square"></i>
                    <p>
                      Forms
                      <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="./forms/general.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>General Elements</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-table"></i>
                    <p>
                      Tables
                      <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="./tables/simple.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Simple Tables</p>
                      </a>
                    </li>
                  </ul>
                </li> --}}
                {{-- <li class="nav-header">EXAMPLES</li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-box-arrow-in-right"></i>
                    <p>
                      Auth
                      <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-in-right"></i>
                        <p>
                          Version 1
                          <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <a href="./examples/login.html" class="nav-link">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Login</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="./examples/register.html" class="nav-link">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Register</p>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li class="nav-item">
                      <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-in-right"></i>
                        <p>
                          Version 2
                          <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <a href="./examples/login-v2.html" class="nav-link">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Login</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="./examples/register-v2.html" class="nav-link">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Register</p>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li class="nav-item">
                      <a href="./examples/lockscreen.html" class="nav-link">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>Lockscreen</p>
                      </a>
                    </li>
                  </ul>
                </li> --}}
                {{-- <li class="nav-item">
                    <a class="nav-link" href="#">Premium Features
                      <span class="badge bg-warning text-dark rounded-pill">Soon</span>
                      <span class="badge rounded-pill bg-info text-uppercase" style="font-size: 0.7rem;">
                      Coming Soon
                    </span>
                    </a>

                    <a class="nav-link" href="#">
                    Analytics
                    <sup class="badge rounded-pill text-bg-primary pulse-chip ms-1" style="font-size: 0.7rem;">Coming Soon</sup>
                    </a>


                </li> --}}

                {{-- <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-flask"></i>
                    <p>
                      Labs
                      <span class="badge badge-outline-info right" style="font-size: 0.6rem; border: 1px solid;">COMING SOON</span>
                    </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-chart-pie"></i>
                    <p>
                      Analytics <sup class="text-danger text-xs"><b>COMING SOON</b></sup>
                    </p>
                  </a>
                </li> --}}

              <li class="nav-header">EXAMS</li>
              <li class="nav-item">
                <a href="{{ route('exams.show', ['examName' => 'ielts']) }}" class="nav-link">
                  <i class="nav-icon bi bi-journal-check"></i>
                  <p>IELTS 
                    {{-- <sup class="text-success text-xs"><b>READY</b></sup> --}}
                  <sup class="badge rounded-pill text-bg-success pulse-chip ms-1" style="font-size: 0.7rem;">Ready</sup>
                </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('exams.show', ['examName' => 'pte']) }}" class="nav-link">
                  <i class="nav-icon bi bi-grip-horizontal"></i>
                  <p>PTE <sup class="text-warning text-xs"><b>TBA</b></sup></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('exams.show', ['examName' => 'gre']) }}" class="nav-link">
                  <i class="nav-icon bi bi-grip-horizontal"></i>
                  <p>GRE <sup class="text-warning text-xs"><b>TBA</b></sup></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('exams.show', ['examName' => 'duolingo']) }}" class="nav-link">
                  <i class="nav-icon bi bi-grip-horizontal"></i>
                  <p>Duolingo <sup class="text-warning text-xs"><b>TBA</b></sup></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('exams.show', ['examName' => 'gmat']) }}" class="nav-link">
                  <i class="nav-icon bi bi-grip-horizontal"></i>
                  <p>GMAT <sup class="text-warning text-xs"><b>TBA</b></sup></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('exams.show', ['examName' => 'bangladesh_bank_ad']) }}" class="nav-link">
                  <i class="nav-icon bi bi-bank"></i>
                  <p>Bangladesh Bank AD
                    <sup class="badge rounded-pill text-bg-success pulse-chip ms-1" style="font-size: 0.7rem;">Ready</sup>
                  </p>
                </a>
              </li>

              <li class="nav-header">Services</li>
              <li class="nav-item">
                <a href="{{ route('sop.index') }}"
                   class="nav-link {{ request()->routeIs('sop.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-file-earmark-text-fill text-info"></i>
                  <p>SOP Service
                    <sup class="badge rounded-pill text-bg-info ms-1" style="font-size: 0.7rem;">New</sup>
                  </p>
                </a>
              </li>

              <li class="nav-header">Opportunities</li>
              <li class="nav-item">
                <a href="{{ route('scholarships.index') }}"
                   class="nav-link {{ request()->routeIs('scholarships.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-mortarboard-fill text-warning"></i>
                  <p>Scholarships
                    <sup class="badge rounded-pill text-bg-warning ms-1" style="font-size: 0.7rem;">New</sup>
                  </p>
                </a>
              </li>

              @if(Auth::user()->role === 'admin')
                <li class="nav-header">Administration</li>

                <li class="nav-item">
                  <a href="{{ route('admin.user.list') }}"
                     class="nav-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-people-fill"></i>
                    <p>Users</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.exams.index') }}"
                     class="nav-link {{ request()->routeIs('admin2.exams.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-mortarboard-fill"></i>
                    <p>Exams</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.modules.index') }}"
                     class="nav-link {{ request()->routeIs('admin.modules.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-collection-fill"></i>
                    <p>Modules</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.questions.index') }}"
                     class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-patch-question-fill"></i>
                    <p>Questions</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.question-options.index') }}"
                     class="nav-link {{ request()->routeIs('admin.question-options.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-ui-radios"></i>
                    <p>Question Options</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.question-groups.index') }}"
                     class="nav-link {{ request()->routeIs('admin.question-groups.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-diagram-3-fill"></i>
                    <p>Question Groups</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.question-group-blocks.index') }}"
                     class="nav-link {{ request()->routeIs('admin.question-group-blocks.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-layout-text-sidebar-reverse"></i>
                    <p>Group Blocks</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.payments.index') }}"
                     class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-cash-coin"></i>
                    <p>Payments</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.referral-programs.index') }}"
                     class="nav-link {{ request()->routeIs('admin.referral-programs.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-gift-fill"></i>
                    <p>Referral Programs</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.scholarships.index') }}"
                     class="nav-link {{ request()->routeIs('admin.scholarships.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-mortarboard-fill"></i>
                    <p>Scholarships</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('admin.evaluations.index') }}"
                     class="nav-link {{ request()->routeIs('admin.evaluations.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-clipboard-check"></i>
                    <p>Evaluations/SOP Assignment</p>
                  </a>
                </li>

              @endif

              {{-- {{ dd(Auth::user()) }} --}}
              @if(Auth::user()->role === 'evaluator')
                <li class="nav-header">Evaluator</li>
                <li class="nav-item">
                  <a href="{{ route('evaluator.evaluations.index') }}"
                     class="nav-link {{ request()->routeIs('evaluator.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-clipboard-check"></i>
                    <p>My Evaluations</p>
                  </a>
                </li>
              @endif

              {{-- <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-ui-checks-grid"></i>
                  <p>
                    Components
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./docs/components/main-header.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Main Header</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./docs/components/main-sidebar.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Main Sidebar</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-filetype-js"></i>
                  <p>
                    Javascript
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./docs/javascript/treeview.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Treeview</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="./docs/browser-support.html" class="nav-link">
                  <i class="nav-icon bi bi-browser-edge"></i>
                  <p>Browser Support</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./docs/how-to-contribute.html" class="nav-link">
                  <i class="nav-icon bi bi-hand-thumbs-up-fill"></i>
                  <p>How To Contribute</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./docs/faq.html" class="nav-link">
                  <i class="nav-icon bi bi-question-circle-fill"></i>
                  <p>FAQ</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./docs/license.html" class="nav-link">
                  <i class="nav-icon bi bi-patch-check-fill"></i>
                  <p>License</p>
                </a>
              </li> --}}

             


              <li class="nav-header">PROFILE</li>
              <li class="nav-item">
                <a href="{{ route('profile') }}" class="nav-link">
                  <i class="nav-icon bi bi-person-badge"></i>
                  <p>My Profile</p>
                </a>
              </li>
              <li class="nav-item">
               
                <a href="{{ route('referral.index') }}"
                   class="nav-link {{ request()->routeIs('referral.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-gift-fill text-danger"></i>
                  <p>Refer a Friend</p>
                </a>
              

                {{-- <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>
                        Logout
                        <i class="nav-arrow bi bi-chevron-right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                          <i class="nav-icon bi bi-record-circle-fill"></i>
                          <p>Level 3</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                          <i class="nav-icon bi bi-record-circle-fill"></i>
                          <p>Level 3</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                          <i class="nav-icon bi bi-record-circle-fill"></i>
                          <p>Level 3</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Level 2</p>
                    </a>
                  </li>
                </ul> --}}
              </li>
              <li class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link">
                  <i class="nav-icon bi bi-box-arrow-left"></i>
                  <p>Logout</p>
                </a>
              </li>
              {{-- <li class="nav-header">LABELS</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle text-danger"></i>
                  <p class="text">Important</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle text-warning"></i>
                  <p>Warning</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle text-info"></i>
                  <p>Informational</p>
                </a>
              </li> --}}
              @endif
            </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>