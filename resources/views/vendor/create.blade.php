@extends('layouts.app')

@section('title', 'Tambah Vendor Baru')

@section('content')
    <form action="{{ route('vendor.store') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Informasi Vendor</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Vendor <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                    @error('nama') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nomor_telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi / Catatan</label>
                    <textarea name="deskripsi" id="deskripsi" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-4">
                    <a href="{{ route('vendor.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium shadow-sm">Simpan Vendor</button>
                </div>
            </div>
        </div>
    </form>
@endsection
