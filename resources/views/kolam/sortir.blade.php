@extends('layouts.app')

@section('title', 'Sortir Ikan')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Sortir Ikan</h1>

        <!-- Source Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-bold text-blue-800">Kolam Asal: #{{ $sourceKolam->nomor_kolam }}</h3>
            <p class="text-sm text-blue-700">Jumlah Ikan Saat Ini: <strong>{{ $sourceKolam->jumlah }}</strong> ekor</p>
            <p class="text-sm text-blue-700">Ukuran Saat Ini: {{ $sourceKolam->ukuran_ikan }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('kolam.processSortir') }}" method="POST">
                @csrf
                <input type="hidden" name="source_kolam_id" value="{{ $sourceKolam->id }}">

                <!-- Destination -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kolam Tujuan</label>
                    <select name="dest_kolam_id" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                        <option value="">-- Pilih Kolam --</option>
                        @foreach($destKolams as $dest)
                            <option value="{{ $dest->id }}">
                                Kolam #{{ $dest->nomor_kolam }} (Isi: {{ $dest->jumlah }} ekor, Status: {{ $dest->status }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- Qty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Dipindah</label>
                        <input type="number" name="jumlah_pindah" max="{{ $sourceKolam->jumlah }}" min="1" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                    </div>

                    <!-- New Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Baru (di Tujuan)</label>
                        <input type="text" name="ukuran_baru" placeholder="Contoh: 6-8 cm" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                        <p class="text-xs text-gray-500 mt-1">Ukuran di kolam tujuan akan diupdate ke nilai ini.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('kolam.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">Proses Sortir</button>
                </div>
            </form>
        </div>
    </div>
@endsection
