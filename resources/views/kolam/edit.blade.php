@extends('layouts.app')

@section('title', 'Ubah Status Kolam')

@section('content')
    <form action="{{ route('kolam.update', $kolam) }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        @method('PUT')
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
            <h2 class="text-lg font-semibold text-gray-800">Ubah Status Kolam {{ $kolam->nomor_kolam }}</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor Kolam</label>
                    <input type="text" class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" value="{{ $kolam->nomor_kolam }}" readonly disabled>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Ikan</label>
                    <input type="text" class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" value="{{ $kolam->persediaan->nama_barang ?? '-' }}" readonly disabled>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Ikan Saat Ini</label>
                    <input type="text" class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" value="{{ number_format($kolam->jumlah) }} ekor" readonly disabled>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Kolam <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="panen">Panen</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Catatan: Status hanya dapat diubah secara manual menjadi **Panen**.</p>
                    @error('status') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('kolam.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium shadow-sm">Ubah Status</button>
            </div>
        </div>
    </form>
@endsection
