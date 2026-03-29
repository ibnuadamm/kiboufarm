@extends('layouts.app')

@section('title', 'Catat Pembelian Baru')

@section('content')
    <form action="{{ route('pembelian.store') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <!-- Saldo Info -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg flex justify-between items-center text-blue-900 border border-blue-100">
                <span class="font-medium">Saldo Kas Saat Ini:</span>
                <span class="text-xl font-bold">Rp {{ number_format($modal->saldo_modal, 0, ',', '.') }}</span>
            </div>

            <h2 class="text-lg font-semibold text-gray-800 mb-6">Detail Pembelian</h2>
            
            <div class="space-y-4">
                <!-- Vendor -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="vendor_id" class="block text-sm font-medium text-gray-700">Pilih Vendor <span class="text-red-500">*</span></label>
                        <a href="{{ route('vendor.create') }}" class="text-xs font-bold text-brand-600 hover:text-brand-800 bg-brand-50 px-2 py-1 rounded-lg border border-brand-200 transaction">+ Tambah Vendor</a>
                    </div>
                    <select name="vendor_id" id="vendor_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->nama }}</option>
                        @endforeach
                    </select>
                    @error('vendor_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Barang -->
                <div>
                    <label for="persediaan_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang <span class="text-red-500">*</span></label>
                    <select name="persediaan_id" id="persediaan_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" onchange="checkKategori()" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($persediaans as $item)
                            <option value="{{ $item->id }}" data-kategori="{{ $item->kategori }}" {{ old('persediaan_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_barang }} ({{ $item->kategori }})
                            </option>
                        @endforeach
                    </select>
                    @error('persediaan_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Jumlah -->
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                        @error('jumlah') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Harga Satuan -->
                    <div>
                        <label for="harga_satuan" class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" id="harga_satuan" value="{{ old('harga_satuan') }}" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                        @error('harga_satuan') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal_pembelian" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                </div>

                <!-- FORM Create Kolam (Hidden by default) -->
                <div id="form-kolam" class="hidden mt-6 p-4 bg-teal-50 border border-teal-200 rounded-lg">
                    <h3 class="text-md font-bold text-teal-800 mb-3">Otomatis Tebar Benih ke Kolam</h3>
                    <p class="text-sm text-teal-600 mb-4">Karena Anda membeli <strong>Bibit</strong>, silakan isi data kolam untuk memulai siklus baru.</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nomor_kolam" class="block text-sm font-medium text-teal-800 mb-1">Nomor Kolam <span class="text-red-500">*</span></label>
                            <input type="number" name="nomor_kolam" id="nomor_kolam" placeholder="Contoh: 1" class="w-full rounded-md border-teal-300 focus:border-teal-500 focus:ring-teal-500">
                        </div>
                        <div>
                            <label for="ukuran_ikan" class="block text-sm font-medium text-teal-800 mb-1">Ukuran Ikan <span class="text-red-500">*</span></label>
                            <input type="text" name="ukuran_ikan" id="ukuran_ikan" placeholder="Contoh: 5-7 cm" class="w-full rounded-md border-teal-300 focus:border-teal-500 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-4">
                    <a href="{{ route('pembelian.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium shadow-sm">Simpan Transaksi</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function checkKategori() {
            const select = document.getElementById('persediaan_id');
            const selectedOption = select.options[select.selectedIndex];
            const kategori = selectedOption.getAttribute('data-kategori');
            const formKolam = document.getElementById('form-kolam');
            const inputNoKolam = document.getElementById('nomor_kolam');
            const inputUkuran = document.getElementById('ukuran_ikan');

            if (kategori === 'Bibit') {
                formKolam.classList.remove('hidden');
                // Make required if Bibit
                inputNoKolam.required = true;
                inputUkuran.required = true;
            } else {
                formKolam.classList.add('hidden');
                inputNoKolam.required = false;
                inputUkuran.required = false;
                inputNoKolam.value = '';
                inputUkuran.value = '';
            }
        }
    </script>
@endsection
