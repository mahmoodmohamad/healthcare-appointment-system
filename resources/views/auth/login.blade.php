<!doctype html>
<html lang="en" class="layout-wide customizer-hide" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Login - Materio Dashboard</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Vendor & Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers & Config -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>
<body>
<div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6 mx-4">
            <!-- Login -->
            <div class="card p-sm-7 p-2">
                <div class="app-brand justify-content-center mt-5">
                    <a href="#" class="app-brand-link gap-3">
                        <span class="app-brand-logo demo">
                            <span class="text-primary">
                                <!-- SVG Logo Here -->
                            </span>
                        </span>
                        <span class="app-brand-text demo text-heading fw-semibold">Materio</span>
                    </a>
                </div>

                <div class="card-body mt-1">
                    <h4 class="mb-1">Welcome! 👋🏻</h4>
                    <p class="mb-5">Sign in to start your session</p>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('login.custom') }}" method="POST" class="mb-5">
                        @csrf
                        <div class="form-floating form-floating-outline mb-5">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" value="{{ old('email') }}" required autofocus>
                            <label for="email">Email</label>
                            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-password-toggle mb-5">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••••••" required>
                                    <label for="password">Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer">
                                    <i class="icon-base ri ri-eye-off-line icon-20px"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-5 pb-2 d-flex justify-content-between align-items-center">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember-me">
                                <label class="form-check-label" for="remember-me">Remember Me</label>
                            </div>
                            <a href="#" class="float-end mb-1">Forgot Password?</a>
                        </div>

                        <div class="mb-5">
                            <button type="submit" class="btn btn-primary d-grid w-100">Login</button>
                        </div>
                    </form>

                    <p class="text-center mb-5">
                        <span>New here?</span>
                        <a href="#">Create an account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
