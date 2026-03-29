@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        
        {{-- 1. Summary Cards (Penjualan) --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Ringkasan Penjualan (Bulan Ini)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Omset -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Omset</p>
                            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sales Count -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $summary['total_transaksi'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Fish Sold -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                           <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Ikan Terjual</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($summary['total_ikan_terjual'], 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">ekor</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Chart Section --}}
        <div class="pt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Grafik Kematian Ikan (Bulan Ini)</h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="relative h-80 w-full">
                    <canvas id="deathChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('deathChart').getContext('2d');
        const deathChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Jumlah Kematian (Ekor)',
                    data: {!! json_encode($data) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.2)', // Red-500 with opacity
                    borderColor: 'rgba(239, 68, 68, 1)', // Red-500
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(239, 68, 68, 1)',
                    pointRadius: 4,
                    tension: 0.3, // Curve
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            borderDash: [2, 4],
                            color: '#e5e7eb'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    </script>
@endsection
