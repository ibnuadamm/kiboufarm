@extends('layouts.app')

@section('title', 'Laporan Pendapatan per Kolam')

@section('content')
    <div class="mb-6 no-print">
        <form action="{{ route('laporan-pendapatan-kolam.index') }}" method="GET" class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-wrap gap-4 items-end">
            <div>
                <label for="kolam_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kolam</label>
                <select name="kolam_id" id="kolam_id" class="rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                    <option value="all" {{ $kolamIdFilter === 'all' ? 'selected' : '' }}>Semua Kolam</option>
                    @foreach($allKolams as $kolamTersedia)
                        <option value="{{ $kolamTersedia->id }}" {{ $kolamIdFilter == $kolamTersedia->id ? 'selected' : '' }}>
                            Kolam {{ $kolamTersedia->nomor_kolam }}
                        </option>
                    @endforeach
                </select>
            </div>
            
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
            <a href="{{ route('laporan-pendapatan-kolam.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium px-4 py-2">
                Reset
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
            <h3 class="text-xl font-semibold text-gray-700 mt-1">Laporan Pendapatan Tiap Kolam</h3>
            <p class="text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        <div class="p-8">
            @if(count($laporanData) > 0)
                <div class="space-y-10">
                    @foreach($laporanData as $data)
                        <div class="border border-gray-200 rounded-lg p-6 bg-white shadow-sm print:shadow-none print:border-gray-300">
                            <h4 class="text-xl font-bold text-brand-700 mb-6 border-b border-brand-200 pb-2">
                                Laporan Pendapatan Kolam {{ $data['kolam']->nomor_kolam }}
                            </h4>
                            
                            {{-- PENDAPATAN --}}
                            <div class="mb-6 pl-4 border-l-4 border-green-500">
                                <div class="flex justify-between items-center text-lg">
                                    <span class="font-semibold text-gray-800">Pendapatan (dari penjualan)</span>
                                    <span class="font-mono font-bold text-green-700">Rp {{ number_format($data['pendapatan'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            {{-- BEBAN --}}
                            <div class="mb-4 pl-4 border-l-4 border-red-500">
                                <h5 class="font-semibold text-gray-800 mb-3 text-lg">Beban:</h5>
                                <div class="space-y-2 ml-4">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Beban Vitamin Kolam {{ $data['kolam']->nomor_kolam }}</span>
                                        <span class="font-mono">Rp {{ number_format($data['beban_vitamin'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Beban Obat Kolam {{ $data['kolam']->nomor_kolam }}</span>
                                        <span class="font-mono">Rp {{ number_format($data['beban_obat'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Beban Pakan Kolam {{ $data['kolam']->nomor_kolam }}</span>
                                        <span class="font-mono">Rp {{ number_format($data['beban_pakan'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Beban Pembelian Bibit Kolam {{ $data['kolam']->nomor_kolam }}</span>
                                        <span class="font-mono">Rp {{ number_format($data['beban_bibit'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Beban Kematian Ikan Kolam {{ $data['kolam']->nomor_kolam }}</span>
                                        <span class="font-mono">Rp {{ number_format($data['beban_kematian'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-800 font-semibold pt-2 border-t border-gray-100 mt-2">
                                        <span>Total Beban Kolam {{ $data['kolam']->nomor_kolam }}</span>
                                        <span class="font-mono text-red-600">Rp {{ number_format($data['total_beban'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- PENDAPATAN BERSIH --}}
                            <div class="mt-6 pt-4 border-t-2 border-gray-800 bg-gray-50 -mx-6 -mb-6 p-6 rounded-b-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold uppercase tracking-wider {{ $data['pendapatan_bersih'] >= 0 ? 'text-green-800' : 'text-red-800' }}">
                                        Pendapatan Bersih Kolam {{ $data['kolam']->nomor_kolam }}
                                    </span>
                                    <span class="text-2xl font-bold font-mono {{ $data['pendapatan_bersih'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                        Rp {{ number_format($data['pendapatan_bersih'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada pendapatan atau beban pada periode yang dipilih.</p>
                </div>
            @endif
        </div>
        
        <div class="bg-gray-50 p-4 text-center text-xs text-gray-400 border-t border-gray-200 print:hidden mt-8">
            Generated by System at {{ now()->format('d M Y H:i') }}
        </div>
    </div>
    
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .shadow-lg, .shadow-sm { box-shadow: none !important; }
            nav, aside { display: none !important; }
            main { margin: 0 !important; padding: 0 !important; }
            .min-h-screen { height: auto !important; }
            .print\:border-gray-300 { border-color: #d1d5db !important; }
        }
    </style>
@endsection
