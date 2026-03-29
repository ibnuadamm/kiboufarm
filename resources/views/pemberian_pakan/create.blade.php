@extends('layouts.app')

@section('title', 'Catat Pemberian Pakan/Obat')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Catat Penggunaan Pakan/Obat</h1>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('pemberian-pakan.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kolam</label>
                    <select name="kolam_id" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                        <option value="">-- Pilih Kolam Aktif --</option>
                        @foreach($kolams as $kolam)
                            <option value="{{ $kolam->id }}">Kolam #{{ $kolam->nomor_kolam }} ({{ ucfirst(str_replace('_', ' ', $kolam->status)) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang (Pakan/Obat)</label>
                    <select name="persediaan_id" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required id="barangSelect">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $barang)
                            <option value="{{ $barang->id }}" data-satuan="{{ $barang->satuan }}" data-sisa="{{ $barang->stok_sisa }}">
                                {{ $barang->nama_barang }} ({{ $barang->kategori }}) - Sisa: {{ number_format($barang->stok_sisa) }} {{ $barang->satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penggunaan</label>
                    <div class="flex">
                        <input type="number" name="jumlah" class="w-full rounded-l-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required min="1">
                        <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm" id="satuanLabel">
                            -
                        </span>
                    </div>
                    @error('stok')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Optional)</label>
                    <textarea name="catatan" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('pemberian-pakan.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('barangSelect').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const satuan = option.getAttribute('data-satuan') || '-';
            document.getElementById('satuanLabel').textContent = satuan;
        });
    </script>
@endsection
