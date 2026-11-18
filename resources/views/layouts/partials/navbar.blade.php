<nav
    class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)" aria-label="{{ __('Toggle navigation') }}">
            <i class="icon-base ri ri-menu-line icon-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
            {{-- Language switcher --}}
            <li class="nav-item dropdown me-3">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                   href="#"
                   id="languageDropdown"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false"
                   aria-label="{{ __('Change language') }}">
                    <i class="ri ri-global-line"></i>
                    <span>{{ app()->isLocale('ar') ? 'العربية' : 'English' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ app()->isLocale('ar') ? 'active' : '' }}"
                           href="{{ route('language.switch', ['locale' => 'ar']) }}"
                           lang="ar"
                           hreflang="ar">
                            <span>العربية</span>
                            @if (app()->isLocale('ar'))
                                <i class="ri ri-check-line"></i>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ app()->isLocale('en') ? 'active' : '' }}"
                           href="{{ route('language.switch', ['locale' => 'en']) }}"
                           lang="en"
                           hreflang="en">
                            <span>English</span>
                            @if (app()->isLocale('en'))
                                <i class="ri ri-check-line"></i>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            {{-- User menu --}}
            @auth
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0"
                       href="javascript:void(0);"
                       data-bs-toggle="dropdown"
                       aria-label="{{ __('Open user menu') }}">
                        <div class="avatar avatar-online">
                            <img src="{{ asset('assets/img/avatars/1.png') }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ asset('assets/img/avatars/1.png') }}"
                                                 alt="{{ auth()->user()->name }}"
                                                 class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                        <small class="text-body-secondary">{{ __(ucfirst(auth()->user()->role ?? 'User')) }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li><div class="dropdown-divider my-1"></div></li>
                        <li>
                            <div class="d-grid px-4 pt-2 pb-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger d-flex align-items-center">
                                        <small class="align-middle">{{ __('Logout') }}</small>
                                        <i class="ri ri-logout-box-r-line ms-2 ri-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </div>
</nav>
