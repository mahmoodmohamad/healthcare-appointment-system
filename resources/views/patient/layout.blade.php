<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Patient Portal' }} · Healthcare</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        :root { --patient-primary: #2563eb; --patient-ink: #172033; --patient-muted: #64748b; --patient-bg: #f5f8fc; }
        body { margin: 0; background: var(--patient-bg); color: var(--patient-ink); font-family: Inter, system-ui, sans-serif; }
        .patient-shell { min-height: 100vh; display: grid; grid-template-columns: 250px 1fr; }
        .patient-sidebar { background: #102a43; color: #fff; padding: 28px 20px; }
        .patient-brand { font-size: 1.2rem; font-weight: 800; margin-bottom: 36px; }
        .patient-brand small { display: block; color: #9fb3c8; font-size: .75rem; font-weight: 500; margin-top: 5px; }
        .patient-nav a { display: block; color: #d9e2ec; text-decoration: none; padding: 12px 14px; border-radius: 9px; margin-bottom: 7px; }
        .patient-nav a:hover, .patient-nav a.active { background: #1f4f7a; color: #fff; }
        .patient-main { padding: 34px; max-width: 1450px; width: 100%; box-sizing: border-box; }
        .patient-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .patient-topbar h1 { margin: 0; font-size: 1.85rem; }
        .patient-user { color: var(--patient-muted); }
        .patient-card { background: #fff; border: 1px solid #e5edf5; border-radius: 16px; padding: 22px; box-shadow: 0 5px 18px rgba(16, 42, 67, .05); }
        .patient-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px; }
        .patient-stat-label { color: var(--patient-muted); font-size: .82rem; }
        .patient-stat-value { display: block; font-size: 1.8rem; font-weight: 800; margin-top: 8px; color: var(--patient-primary); }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table th, .patient-table td { padding: 13px 10px; border-bottom: 1px solid #edf2f7; text-align: left; }
        .patient-table th { color: var(--patient-muted); font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; }
        .patient-badge { display: inline-block; padding: 5px 9px; border-radius: 999px; background: #e8f1ff; color: #1d4ed8; font-size: .78rem; font-weight: 700; }
        @media (max-width: 900px) { .patient-shell { grid-template-columns: 1fr; } .patient-sidebar { padding: 18px; } .patient-nav { display: flex; flex-wrap: wrap; gap: 6px; } .patient-nav a { margin: 0; } .patient-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 540px) { .patient-main { padding: 20px 14px; } .patient-grid { grid-template-columns: 1fr; } .patient-topbar { display: block; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="patient-shell">
    <aside class="patient-sidebar">
        <div class="patient-brand">Healthcare Portal<small>Patient space</small></div>
        <nav class="patient-nav" aria-label="Patient navigation">
            <a class="active" href="{{ route('patient.dashboard') }}">Overview</a>
            <a href="#appointments">My appointments</a>
            <a href="#medical-history">Medical history</a>
            <a href="#profile">My profile</a>
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 26px;">
                @csrf
                <button type="submit" style="background: transparent; border: 0; color: #d9e2ec; padding: 12px 14px; cursor: pointer; font: inherit;">Sign out</button>
            </form>
        </nav>
    </aside>
    <main class="patient-main">
        <header class="patient-topbar">
            <div>
                <h1>{{ $heading ?? 'Your healthcare overview' }}</h1>
                <div class="patient-user">{{ auth()->user()->name }}</div>
            </div>
            <div class="patient-user">{{ now()->format('l, F j, Y') }}</div>
        </header>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">Please review the highlighted information.</div>
        @endif
        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
