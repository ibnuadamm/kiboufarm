@extends('layouts.app')

@section('title', 'Jurnal Umum')

@section('content')
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-4">
            Daftar transaksi jurnal umum (Debit/Kredit). Dibuat otomatis dari transaksi.
        </div>
        
        <form action="{{ route('jurnal.index') }}" method="GET" class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-wrap gap-4 items-end">
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
            <a href="{{ route('jurnal.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium px-4 py-2">
                Reset
            </a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                    <th class="px-6 py-4 w-32">Tanggal</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4 w-48">Akun / Referensi</th>
                    <th class="px-6 py-4 text-right w-36">Debit</th>
                    <th class="px-6 py-4 text-right w-36">Kredit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($jurnals as $jurnal)
                    {{-- Header Row (Optional, usually we just list details) --}}
                    {{-- Iterating details for flat view or nested view. Let's do a nested-like view for clarity --}}
                    <tr class="bg-gray-50/50">
                        <td class="px-6 py-4 font-medium text-gray-900 border-t border-gray-100" colspan="5">
                            <span class="mr-2">{{ $jurnal->tanggal->format('d/m/Y') }}</span>
                            {{ $jurnal->keterangan }}
                        </td>
                    </tr>
                    @foreach($jurnal->details as $detail)
                        <tr class="hover:bg-gray-50 bg-white transition-colors">
                            <td class="px-6 py-2"></td>
                            <td class="px-6 py-2"></td>
                            <td class="px-6 py-2 font-mono text-sm text-gray-700">
                                {{ $detail->akun->kode }} - {{ $detail->akun->nama }}
                            </td>
                            <td class="px-6 py-2 text-right text-gray-700">
                                @if($detail->debit > 0)
                                    Rp {{ number_format($detail->debit, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-6 py-2 text-right text-gray-700">
                                @if($detail->kredit > 0)
                                    Rp {{ number_format($detail->kredit, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada data jurnal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $jurnals->links() }}
    </div>
@endsection
