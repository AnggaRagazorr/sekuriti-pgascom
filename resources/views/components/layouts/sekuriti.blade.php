<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SEKURITI' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<body class="bg-gray-100">
<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
        <div class="px-4 py-4 border-b border-gray-800">
            <div class="text-lg font-bold">SEKURITI</div>
            <div class="text-xs text-gray-300">Monitoring & Patrol System</div>
        </div>

        <nav class="flex-1 px-2 py-3 space-y-1">
            @php $role = auth()->user()->role ?? null; @endphp

            {{-- Menu Security --}}
            @if($role === 'security')
                <a href="{{ route('security.dashboard') }}"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Dashboard
                </a>

                <a href="{{ route('security.patrol') }}"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Patroli
                </a>

                <a href="{{ route('security.daily-report') }}"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Rekap Harian
                </a>

                <a href="{{ route('security.carpool') }}"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Carpool
                </a>

                <a href="{{ route('security.document-log') }}"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Rekap Dokumen
                </a>
            @endif

            {{-- Menu Monitoring/Admin --}}
            @if($role === 'monitoring')
                <a href="{{ route('monitoring.dashboard') }}"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Dashboard
                </a>

                <a href="{{ route('monitoring.patrols.index') }}"
                   class="block rounded px-3 py-2 hover:bg-gray-800 {{ request()->routeIs('monitoring.patrols.*') ? 'bg-gray-800' : '' }}">
                    Monitoring Patroli
                </a>

                </a>

                <a href="#"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Rekap Harian (Cetak)
                </a>

                <a href="#"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Carpool Logs
                </a>

                <a href="#"
                   class="block rounded px-3 py-2 hover:bg-gray-800">
                    Dokumen Logs
                </a>
            @endif
        </nav>

        <div class="px-4 py-4 border-t border-gray-800 text-sm">
            <div class="text-gray-300">Login sebagai:</div>
            <div class="font-semibold">{{ auth()->user()->name ?? '-' }}</div>
            <div class="text-gray-400">{{ auth()->user()->role ?? '-' }}</div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col">
        {{-- Topbar --}}
        <header class="h-14 bg-white border-b flex items-center justify-between px-6">
            <div class="font-semibold text-gray-800">
                {{ $header ?? 'Dashboard' }}
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-sm px-3 py-2 rounded bg-gray-900 text-white hover:bg-gray-800">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
