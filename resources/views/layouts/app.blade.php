<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aset') — Aset HRGA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .stat { font-size: 1.6rem; font-weight: 700; }
        .table th { white-space: nowrap; font-size: .85rem; color: #6c757d; font-weight: 600; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">Aset HRGA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('aset.*') ? 'active' : '' }}" href="{{ route('aset.index') }}">Data Aset</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">Kategori</a></li>
            </ul>
            <form method="POST" action="{{ route('logout') }}" class="d-flex align-items-center gap-2">
                @csrf
                <span class="text-white-50 small">{{ auth()->user()->name }}</span>
                <button class="btn btn-sm btn-outline-light">Keluar</button>
            </form>
        </div>
    </div>
</nav>

<main class="container pb-5">
    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('sukses') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
