@extends('layouts.app')
@section('title', 'Edit Pelanggan')
@section('content')
<form action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
        <input type="text" name="nama" class="w-full border rounded p-2" required value="{{ old('nama', $pelanggan->nama) }}">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Tipe <span class="text-red-500">*</span></label>
        <select name="tipe" class="w-full border rounded p-2">
            <option value="Umum" {{ $pelanggan->tipe == 'Umum' ? 'selected' : '' }}>Umum</option>
            <option value="Tengkulak" {{ $pelanggan->tipe == 'Tengkulak' ? 'selected' : '' }}>Tengkulak</option>
            <option value="Restaurant" {{ $pelanggan->tipe == 'Restaurant' ? 'selected' : '' }}>Restaurant</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Telepon</label>
        <input type="text" name="nomor_telepon" class="w-full border rounded p-2" value="{{ old('nomor_telepon', $pelanggan->nomor_telepon) }}">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Alamat</label>
        <textarea name="alamat" class="w-full border rounded p-2">{{ old('alamat', $pelanggan->alamat) }}</textarea>
    </div>
    <div class="flex justify-end gap-2">
        <a href="{{ route('pelanggan.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
        <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded hover:bg-brand-700">Update</button>
    </div>
</form>
@endsection
