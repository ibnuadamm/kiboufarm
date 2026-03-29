<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KibouFarm') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 hidden md:block fixed h-full z-10 overflow-y-auto">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <span class="text-xl font-bold text-brand-700">KibouFarm</span>
            </div>
            <nav class="p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('dashboard') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('persediaan.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('persediaan.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('persediaan.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    Master Persediaan
                </a>
                <a href="{{ route('vendor.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('vendor.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('vendor.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Master Data Vendor
                </a>
                <a href="{{ route('pelanggan.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('pelanggan.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('pelanggan.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Master Pelanggan
                </a>

                <div class="border-t border-gray-200 my-4"></div>

                <a href="{{ route('pembelian.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('pembelian.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('pembelian.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Data Pembelian
                </a>
                <a href="{{ route('kolam.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('kolam.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('kolam.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Master Siklus Kolam
                </a>

                <a href="{{ route('pemberian-pakan.index') }}" class="flex items-center px-4 py-3 text-orange-500 hover:bg-orange-50 hover:text-orange-700 rounded-lg transition-colors group {{ request()->routeIs('pemberian-pakan.*') ? 'bg-orange-50 text-orange-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-orange-300 group-hover:text-orange-500 {{ request()->routeIs('pemberian-pakan.*') ? 'text-orange-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Pemberian Pakan/Obat
                </a>
                <a href="{{ route('kematian.index') }}" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors group {{ request()->routeIs('kematian.*') ? 'bg-red-50 text-red-700 font-medium' : '' }}">
                   <svg class="w-5 h-5 mr-3 text-red-300 group-hover:text-red-500 {{ request()->routeIs('kematian.*') ? 'text-red-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                   </svg>
                   Kematian Ikan
                </a>
                <a href="{{ route('penjualan.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('penjualan.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('penjualan.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Data Penjualan
                </a>
                <a href="{{ route('modal.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('modal.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('modal.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Modal
                </a>
                <a href="{{ route('biaya-operasional.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('biaya-operasional.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('biaya-operasional.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                    Biaya Operasional
                </a>
                <a href="{{ route('jurnal.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('jurnal.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('jurnal.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Jurnal Umum
                </a>
                <a href="{{ route('buku-besar.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('buku-besar.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('buku-besar.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Buku Besar
                </a>
                <a href="{{ route('laba-rugi.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('laba-rugi.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('laba-rugi.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Laporan Laba Rugi
                </a>
                <a href="{{ route('coa.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg transition-colors group {{ request()->routeIs('coa.*') ? 'bg-brand-50 text-brand-700 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-brand-500 {{ request()->routeIs('coa.*') ? 'text-brand-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Master Data Akun (COA)
                </a>
                
                <div class="border-t border-gray-200 my-4"></div>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors font-medium">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 p-8">
            <header class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                </div>
            </header>

            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
