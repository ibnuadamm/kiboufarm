@extends('layouts.app')

@section('title', 'Buku Besar')

@section('content')
    <div class="mb-6">
        <form action="{{ route('buku-besar.index') }}" method="GET" class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-wrap gap-4 items-end">
            <div>
                <label for="akun_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Akun</label>
                <select name="akun_id" id="akun_id" class="rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" onchange="this.form.submit()">
                    <option value="">-- Pilih Akun --</option>
                    @foreach($akuns as $akun)
                        <option value="{{ $akun->id }}" {{ (request('akun_id') == $akun->id) ? 'selected' : '' }}>
                            {{ $akun->kode }} - {{ $akun->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
            </div>

            <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-md hover:bg-brand-700 text-sm font-medium">
                Filter
            </button>
            <a href="{{ route('buku-besar.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium px-4 py-2">
                Reset
            </a>
        </form>
    </div>

    @if($selectedAkun)
        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $selectedAkun->kode }} - {{ $selectedAkun->nama }}
                </h3>
                <span class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    Tipe: {{ $selectedAkun->tipe }}
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold">
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">No. Jurnal</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3 text-right">Debit</th>
                            <th class="px-6 py-3 text-right">Kredit</th>
                            <th class="px-6 py-3 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php 
                            $saldo = 0; 
                            // Determine normal balance side based on type
                            $isDebitNormal = in_array($selectedAkun->tipe, ['Asset', 'Expense']);
                        @endphp
                        
                        @forelse($details as $detail)
                            @php
                                // Calculate running balance
                                // If Debit Normal (Asset/Expense): +Debit -Index
                                // If Credit Normal (Liability/Equity/Revenue): +Credit -Debit
                                if ($isDebitNormal) {
                                    $saldo += ($detail->debit - $detail->kredit);
                                } else {
                                    $saldo += ($detail->kredit - $detail->debit);
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">{{ $detail->jurnal->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 font-mono">#{{ $detail->jurnal->id }}</td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $detail->jurnal->keterangan }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-gray-900">
                                    {{ $detail->debit > 0 ? number_format($detail->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-gray-900">
                                    {{ $detail->kredit > 0 ? number_format($detail->kredit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-right font-bold {{ $saldo < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    Rp {{ number_format(abs($saldo), 0, ',', '.') }} {{ $saldo < 0 ? '(Chg)' : '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Tidak ada transaksi ditemukan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($details->isNotEmpty())
                        <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right">Saldo Akhir</td>
                                <td class="px-6 py-3 text-right">
                                    Rp {{ number_format(abs($saldo), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Pilih Akun</h3>
            <p class="mt-1 text-sm text-gray-500">Silakan pilih akun dari dropdown di atas untuk melihat buku besar.</p>
        </div>
    @endif
@endsection
