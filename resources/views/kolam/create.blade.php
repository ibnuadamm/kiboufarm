@extends('layouts.app')

@section('title', 'Buat Siklus Kolam Baru')

@section('content')
    <form action="{{ route('kolam.store') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Informasi Siklus Tebar</h2>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Nomor Kolam -->
                <div>
                    <label for="nomor_kolam" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kolam <span class="text-red-500">*</span></label>
                    <input type="number" name="nomor_kolam" id="nomor_kolam" value="{{ old('nomor_kolam') }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                    <p class="text-xs text-gray-500 mt-1">Masukkan nomor kolam fisik. (Tidak bisa duplikat jika belum panen)</p>
                    @error('nomor_kolam') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Jenis Ikan (Persediaan) -->
                <div>
                    <label for="persediaan_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Ikan (Bibit) <span class="text-red-500">*</span></label>
                    <select name="persediaan_id" id="persediaan_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                        <option value="">-- Pilih Bibit --</option>
                        @foreach($bibits as $bibit)
                            <option value="{{ $bibit->id }}" {{ old('persediaan_id') == $bibit->id ? 'selected' : '' }}>
                                {{ $bibit->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    @error('persediaan_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Tanggal Tebar -->
                    <div>
                        <label for="tanggal_tebar" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tebar <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_tebar" id="tanggal_tebar" value="{{ old('tanggal_tebar', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                        @error('tanggal_tebar') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Ekor <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                        @error('jumlah') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Ukuran Ikan -->
                <div>
                    <label for="ukuran_ikan" class="block text-sm font-medium text-gray-700 mb-1">Ukuran Ikan <span class="text-red-500">*</span></label>
                    <input type="text" name="ukuran_ikan" id="ukuran_ikan" value="{{ old('ukuran_ikan') }}" placeholder="Contoh: 5-7 cm" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                    @error('ukuran_ikan') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-4">
                    <a href="{{ route('kolam.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium shadow-sm">Simpan Siklus</button>
                </div>
            </div>
        </div>
    </form>
@endsection
