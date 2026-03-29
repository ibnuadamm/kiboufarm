@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
    <div class="mb-6 no-print">
        <form action="{{ route('laba-rugi.index') }}" method="GET" class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-wrap gap-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
            </div>

            <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-md hover:bg-brand-700 text-sm font-medium">
                Filter
            </button>
            <a href="{{ route('laba-rugi.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium px-4 py-2">
                Reset (Bulan Ini)
            </a>
            
            <button type="button" onclick="window.print()" class="ml-auto flex items-center bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / PDF
            </button>
        </form>
    </div>

    <div class="bg-white border rounded-lg overflow-hidden max-w-4xl mx-auto shadow-lg print:shadow-none print:border-none">
        {{-- Header Report --}}
        <div class="p-8 text-center border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 uppercase tracking-wide">KibouFarm</h2>
            <h3 class="text-xl font-semibold text-gray-700 mt-1">Laporan Laba Rugi</h3>
            <p class="text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        <div class="p-8">
            {{-- 1. PENDAPATAN --}}
            <div class="mb-8">
                <h4 class="text-lg font-bold text-brand-700 mb-4 border-b border-brand-200 pb-2">PENDAPATAN</h4>
                <div class="space-y-3">
                    @foreach($pendapatanAccounts as $akun)
                        @if($akun->saldo_periode != 0)
                            <div class="flex justify-between text-gray-800">
                                <span>{{ $akun->kode }} - {{ $akun->nama }}</span>
                                <span class="font-mono">Rp {{ number_format($akun->saldo_periode, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="flex justify-between font-bold text-gray-900 mt-4 pt-4 border-t border-gray-200 bg-gray-50/50 p-2 rounded">
                    <span>TOTAL PENDAPATAN</span>
                    <span class="font-mono">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- 2. HARGA POKOK PENJUALAN (HPP) --}}
            <div class="mb-8">
                <h4 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">HARGA POKOK PENJUALAN (HPP)</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-800">
                        <span>Bibit</span>
                        <span class="font-mono">Rp {{ number_format($hppComponents['Bibit'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-800">
                        <span>Pakan</span>
                        <span class="font-mono">Rp {{ number_format($hppComponents['Pakan'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-800">
                        <span>Obat</span>
                        <span class="font-mono">Rp {{ number_format($hppComponents['Obat'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-800">
                        <span>Vitamin</span>
                        <span class="font-mono">Rp {{ number_format($hppComponents['Vitamin'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="flex justify-between font-bold text-gray-900 mt-4 pt-4 border-t border-gray-200 bg-gray-50/50 p-2 rounded">
                    <span>TOTAL HPP</span>
                    <span class="font-mono">Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- 3. BEBAN OPERASIONAL --}}
            <div class="mb-8">
                <h4 class="text-lg font-bold text-red-700 mb-4 border-b border-red-200 pb-2">BEBAN OPERASIONAL</h4>
                <div class="space-y-3">
                    @foreach($bebanAccounts as $akun)
                        @if($akun->saldo_periode != 0)
                            <div class="flex justify-between text-gray-800">
                                <span>{{ $akun->nama }}</span> {{-- Simplify Name --}}
                                <span class="font-mono">Rp {{ number_format($akun->saldo_periode, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="flex justify-between font-bold text-gray-900 mt-4 pt-4 border-t border-gray-200 bg-gray-50/50 p-2 rounded">
                    <span>TOTAL BEBAN OPERASIONAL</span>
                    <span class="font-mono">Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- 3. SUMMARY --}}
            <div class="mt-8 pt-6 border-t-2 border-gray-900">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold uppercase tracking-wider {{ $labaBersih >= 0 ? 'text-green-800' : 'text-red-800' }}">
                        {{ $labaBersih >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
                    </span>
                    <span class="text-2xl font-bold font-mono {{ $labaBersih >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 text-center text-xs text-gray-400 border-t border-gray-200 print:hidden">
            Generated by System at {{ now()->format('d M Y H:i') }}
        </div>
    </div>
    
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .shadow-lg { box-shadow: none !important; }
            nav, aside { display: none !important; }
            main { margin: 0 !important; padding: 0 !important; }
            .min-h-screen { height: auto !important; }
        }
    </style>
@endsection
