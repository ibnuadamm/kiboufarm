@extends('layouts.app')
@section('title', 'Tambah Pelanggan')
@section('content')
<form action="{{ route('pelanggan.store') }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
        <input type="text" name="nama" class="w-full border rounded p-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Tipe <span class="text-red-500">*</span></label>
        <select name="tipe" class="w-full border rounded p-2">
            <option value="Umum">Umum</option>
            <option value="Tengkulak">Tengkulak</option>
            <option value="Restaurant">Restaurant</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Telepon</label>
        <input type="text" name="nomor_telepon" class="w-full border rounded p-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Alamat</label>
        <textarea name="alamat" class="w-full border rounded p-2"></textarea>
    </div>
    <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded">Simpan</button>
</form>
@endsection
