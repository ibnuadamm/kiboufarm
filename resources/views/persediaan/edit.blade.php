@extends('layouts.app')

@section('title', 'Edit Master Barang')

@section('content')
    <form action="{{ route('persediaan.update', $persediaan) }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $persediaan->nama_barang) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                @error('nama_barang') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" id="kategori" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                    <option value="">Pilih Kategori</option>
                    @foreach(['Pakan', 'Obat', 'Vitamin', 'Alat', 'Bibit', 'Lainnya'] as $kat)
                        <option value="{{ $kat }}" {{ old('kategori', $persediaan->kategori) == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $persediaan->satuan) }}" placeholder="Contoh: Sak, Kg, Liter, Pcs" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                @error('satuan') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('persediaan.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium shadow-sm">Update Barang</button>
            </div>
        </div>
    </form>
@endsection
