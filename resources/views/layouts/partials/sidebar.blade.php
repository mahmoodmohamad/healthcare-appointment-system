<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Brand -->
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo me-1">
                <span class="text-primary">
                    <svg width="30" height="24" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z" fill="currentColor" />
                        <path opacity="0.077704" fill-rule="evenodd" clip-rule="evenodd" d="M0 65.2656L60.4839 99.9629V133.979L0 65.2656Z" fill="black" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z" fill="currentColor" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z" fill="currentColor" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z" fill="currentColor" />
                    </svg>
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2">🏥 Healthcare</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="menu-toggle-icon d-xl-inline-block align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        
        @auth
            {{-- ADMIN SIDEBAR --}}
            @if(auth()->user()->isAdmin())
                <!-- Dashboard -->
                <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="menu-link">
                        <i class="menu-icon icon-base ri-dashboard-line"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                <!-- User Management -->
                <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ri-user-settings-line"></i>
                        <div>User Management</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.index') }}" class="menu-link">
                                <div>All Users</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.create') }}" class="menu-link">
                                <div>Add New User</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Statistics -->
                <li class="menu-item {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                    <a href="{{ route('admin.statistics') }}" class="menu-link">
                        <i class="menu-icon icon-base ri-bar-chart-line"></i>
                        <div>Statistics</div>
                    </a>
                </li>

                <!-- Divider -->
                <li class="menu-header mt-7">
                    <span class="menu-header-text">System</span>
                </li>

                <!-- Logout -->
                <li class="menu-item">
                    <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();" class="menu-link">
                        <i class="menu-icon icon-base ri-logout-box-line"></i>
                        <div>Logout</div>
                    </a>
                </li>
            @endif

            {{-- PHYSICIAN SIDEBAR --}}
            @if(auth()->user()->isPhysician())
                <!-- Dashboard -->
                <li class="menu-item {{ request()->routeIs('physician.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('physician.dashboard') }}" class="menu-link">
                        <i class="menu-icon icon-base ri-dashboard-line"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                <!-- Appointments -->
                <li class="menu-item {{ request()->routeIs('physician.appointments.*') ? 'active' : '' }}">
                    <a href="{{ route('physician.appointments.index') }}" class="menu-link">
                        <i class="menu-icon icon-base ri-calendar-check-line"></i>
                        <div>My Appointments</div>
                    </a>
                </li>

                <!-- Divider -->
                <li class="menu-header mt-7">
                    <span class="menu-header-text">Account</span>
                </li>

                <!-- Logout -->
                <li class="menu-item">
                    <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();" class="menu-link">
                        <i class="menu-icon icon-base ri-logout-box-line"></i>
                        <div>Logout</div>
                    </a>
                </li>
            @endif

            {{-- SECRETARY SIDEBAR --}}
            @if(auth()->user()->isSecretary())
                <!-- Dashboard -->
                <li class="menu-item {{ request()->routeIs('secretary.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('secretary.dashboard') }}" class="menu-link">
                        <i class="menu-icon icon-base ri-dashboard-line"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                <!-- Patients -->
                <li class="menu-item {{ request()->routeIs('patients.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ri-user-heart-line"></i>
                        <div>Patients</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('patients.index') ? 'active' : '' }}">
                            <a href="{{ route('patients.index') }}" class="menu-link">
                                <div>All Patients</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('patients.create') ? 'active' : '' }}">
                            <a href="{{ route('patients.create') }}" class="menu-link">
                                <div>Register New Patient</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Appointments -->
                <li class="menu-item {{ request()->routeIs('appointments.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ri-calendar-line"></i>
                        <div>Appointments</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('appointments.index') ? 'active' : '' }}">
                            <a href="{{ route('appointments.index') }}" class="menu-link">
                                <div>All Appointments</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('appointments.create') ? 'active' : '' }}">
                            <a href="{{ route('appointments.create') }}" class="menu-link">
                                <div>Book Appointment</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('appointments.calendar') ? 'active' : '' }}">
                            <a href="{{ route('appointments.calendar') }}" class="menu-link">
                                <div>Calendar View</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Divider -->
                <li class="menu-header mt-7">
                    <span class="menu-header-text">Account</span>
                </li>

                <!-- Logout -->
                <li class="menu-item">
                    <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();" class="menu-link">
                        <i class="menu-icon icon-base ri-logout-box-line"></i>
                        <div>Logout</div>
                    </a>
                </li>
            @endif

            {{-- PATIENT SIDEBAR --}}
            @if(auth()->user()->isPatient())
                <!-- Dashboard -->
                <li class="menu-item {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                    <a href="#" class="menu-link">
                        <i class="menu-icon icon-base ri-dashboard-line"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                <!-- My Appointments -->
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon icon-base ri-calendar-line"></i>
                        <div>My Appointments</div>
                    </a>
                </li>

                <!-- Medical History -->
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon icon-base ri-file-list-line"></i>
                        <div>Medical History</div>
                    </a>
                </li>

                <!-- Divider -->
                <li class="menu-header mt-7">
                    <span class="menu-header-text">Account</span>
                </li>

                <!-- Logout -->
                <li class="menu-item">
                    <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();" class="menu-link">
                        <i class="menu-icon icon-base ri-logout-box-line"></i>
                        <div>Logout</div>
                    </a>
                </li>
            @endif

            <!-- Hidden Logout Form -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endauth
    </ul>
</aside>