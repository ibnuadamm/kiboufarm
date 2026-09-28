@extends('layouts.app')

@section('title', 'Tambah COA')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah COA Baru</h1>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('coa.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode COA</label>
                    <input type="text" name="kode" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" placeholder="Contoh: 1-103">
                    @error('kode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama COA</label>
                    <input type="text" name="nama" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" placeholder="Contoh: Persediaan Pakan">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe COA</label>
                    <select name="tipe" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                        <option value="Asset">Asset</option>
                        <option value="Liability">Liability</option>
                        <option value="Equity">Equity</option>
                        <option value="Revenue">Revenue</option>
                        <option value="Expense">Expense</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('coa.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">Simpan COA</button>
                </div>
            </form>
        </div>
    </div>
@endsection
