@extends('layouts.app')

@section('title', 'Tambah Kolam Baru')

@section('content')
    <form action="{{ route('kolam.store') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Pendaftaran Identitas Kolam Baru</h2>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Nomor Kolam -->
                <div>
                    <label for="nomor_kolam" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kolam <span class="text-red-500">*</span></label>
                    <input type="number" name="nomor_kolam" id="nomor_kolam" value="{{ old('nomor_kolam') }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                    <p class="text-xs text-gray-500 mt-1">Masukkan nomor kolam fisik baru (harus unik/belum terdaftar).</p>
                    @error('nomor_kolam') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-4">
                    <a href="{{ route('kolam.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium shadow-sm">Daftarkan Kolam</button>
                </div>
            </div>
        </div>
    </form>
@endsection
