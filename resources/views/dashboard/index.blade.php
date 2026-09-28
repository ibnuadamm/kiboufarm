@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        
        {{-- 1. Summary Cards (Penjualan) --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Ringkasan Penjualan (Bulan Ini)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
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
                
                <!-- Pendapatan Bersih -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pendapatan Bersih</p>
                            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($summary['pendapatan_bersih'], 0, ',', '.') }}</p>
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
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Grafik Populasi & Kematian Ikan (Bulan Ini)</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Populasi Ikan Hidup Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-md font-medium text-gray-600 mb-4">Total Ikan Hidup</h4>
                    <div class="relative h-80 w-full">
                        <canvas id="liveFishChart"></canvas>
                    </div>
                </div>

                <!-- Kematian Ikan Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-md font-medium text-gray-600 mb-4">Kematian Ikan</h4>
                    <div class="relative h-80 w-full">
                        <canvas id="deathChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = {!! json_encode($labels) !!};
        const liveFishData = {!! json_encode($data) !!};
        const deathData = {!! json_encode($dataKematian) !!};

        // Gradient for Live Fish Chart
        const ctxLive = document.getElementById('liveFishChart').getContext('2d');
        let gradientLive = ctxLive.createLinearGradient(0, 0, 0, 400);
        gradientLive.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue-500
        gradientLive.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(ctxLive, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Ikan Hidup (Ekor)',
                    data: liveFishData,
                    backgroundColor: gradientLive,
                    borderColor: '#3b82f6',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#3b82f6',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                    tension: 0.4, // Smoother curve
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(17, 24, 39, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#6b7280' },
                        grid: { borderDash: [4, 4], color: '#f3f4f6', drawBorder: false }
                    },
                    x: {
                        ticks: { color: '#6b7280' },
                        grid: { display: false, drawBorder: false }
                    }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false }
            }
        });

        // Gradient for Death Chart
        const ctxDeath = document.getElementById('deathChart').getContext('2d');
        let gradientDeath = ctxDeath.createLinearGradient(0, 0, 0, 400);
        gradientDeath.addColorStop(0, 'rgba(239, 68, 68, 0.5)'); // Red-500
        gradientDeath.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        new Chart(ctxDeath, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ikan Mati (Ekor)',
                    data: deathData,
                    backgroundColor: gradientDeath,
                    borderColor: '#ef4444',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#ef4444',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#ef4444',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(17, 24, 39, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#6b7280' },
                        grid: { borderDash: [4, 4], color: '#f3f4f6', drawBorder: false }
                    },
                    x: {
                        ticks: { color: '#6b7280' },
                        grid: { display: false, drawBorder: false }
                    }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false }
            }
        });
    </script>
@endsection
