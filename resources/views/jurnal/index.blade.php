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
                    <th class="px-6 py-4">Nama COA</th>
                    <th class="px-6 py-4 w-28">Ref</th>
                    <th class="px-6 py-4 text-right w-36">Debit</th>
                    <th class="px-6 py-4 text-right w-36">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jurnals as $jurnal)
                    @foreach($jurnal->details as $index => $detail)
                        <tr class="hover:bg-gray-50 bg-white transition-colors">
                            <!-- Tanggal -->
                            <td class="px-6 py-3 text-sm text-gray-900 align-top {{ $index === 0 ? 'border-t border-gray-200' : '' }}">
                                @if($index === 0)
                                    <span class="font-medium text-gray-900">{{ $jurnal->tanggal->format('d/m/Y') }}</span>
                                @endif
                            </td>

                            <!-- Nama COA -->
                            <td class="px-6 py-3 align-top text-gray-900 {{ $index === 0 ? 'border-t border-gray-200' : '' }} {{ $detail->kredit > 0 ? 'pl-12' : 'font-medium' }}">
                                {{ $detail->coa->nama }}
                            </td>

                            <!-- Ref (Kode COA) -->
                            <td class="px-6 py-3 align-top font-mono text-sm text-gray-500 {{ $index === 0 ? 'border-t border-gray-200' : '' }}">
                                {{ $detail->coa->kode }}
                            </td>

                            <!-- Debit -->
                            <td class="px-6 py-3 align-top text-right text-gray-700 {{ $index === 0 ? 'border-t border-gray-200' : '' }}">
                                @if($detail->debit > 0)
                                    <div class="inline-flex justify-between w-32 text-sm">
                                        <span class="text-gray-400 text-left">Rp</span>
                                        <span class="text-right">{{ number_format($detail->debit, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-300 pr-2">-</span>
                                @endif
                            </td>

                            <!-- Kredit -->
                            <td class="px-6 py-3 align-top text-right text-gray-700 {{ $index === 0 ? 'border-t border-gray-200' : '' }}">
                                @if($detail->kredit > 0)
                                    <div class="inline-flex justify-between w-32 text-sm">
                                        <span class="text-gray-400 text-left">Rp</span>
                                        <span class="text-right">{{ number_format($detail->kredit, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-300 pr-2">-</span>
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
