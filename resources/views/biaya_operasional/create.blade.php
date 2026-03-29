@extends('layouts.app')

@section('title', 'Catat Biaya Operasional')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Input Pengeluaran Operasional</h1>

        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Saldo Modal Tersedia: <span class="font-bold">Rp {{ number_format($saldoModal, 0, ',', '.') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('biaya-operasional.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                        <option value="Listrik">Listrik</option>
                        <option value="Air">Air</option>
                        <option value="Gaji Karyawan">Gaji Karyawan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Bayar Listrik Bulan Januari" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Biaya (Rp)</label>
                    <input type="number" name="jumlah" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required min="1">
                    @error('saldo')
                        <p class="text-red-500 text-sm mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('biaya-operasional.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
@endsection
