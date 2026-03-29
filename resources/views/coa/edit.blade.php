@extends('layouts.app')

@section('title', 'Edit Akun COA')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Akun</h1>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('coa.update', $coa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Akun</label>
                    <input type="text" name="kode" value="{{ $coa->kode }}" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                    @error('kode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Akun</label>
                    <input type="text" name="nama" value="{{ $coa->nama }}" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Akun</label>
                    <select name="tipe" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                        <option value="Asset" {{ $coa->tipe == 'Asset' ? 'selected' : '' }}>Asset</option>
                        <option value="Liability" {{ $coa->tipe == 'Liability' ? 'selected' : '' }}>Liability</option>
                        <option value="Equity" {{ $coa->tipe == 'Equity' ? 'selected' : '' }}>Equity</option>
                        <option value="Revenue" {{ $coa->tipe == 'Revenue' ? 'selected' : '' }}>Revenue</option>
                        <option value="Expense" {{ $coa->tipe == 'Expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('coa.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">Update Akun</button>
                </div>
            </form>
        </div>
    </div>
@endsection
