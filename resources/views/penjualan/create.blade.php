@extends('layouts.app')

@section('title', 'Catat Penjualan (Panen)')

@section('content')
    <form action="{{ route('penjualan.store') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-700">
                    Mencatat penjualan akan otomatis mengubah status kolam yang dipilih menjadi <strong>Panen</strong>.
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
            
            <!-- Pilih Kolam -->
            <div>
                <label for="kolam_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kolam Panen <span class="text-red-500">*</span></label>
                <select name="kolam_id" id="kolam_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                    <option value="">-- Pilih Kolam Siap Panen --</option>
                    @foreach($kolams as $kolam)
                        <option value="{{ $kolam->id }}" data-jumlah="{{ $kolam->jumlah }}" {{ old('kolam_id') == $kolam->id ? 'selected' : '' }}>
                            Kolam {{ $kolam->nomor_kolam }} - {{ $kolam->persediaan->nama_barang ?? 'Tanpa Bibit' }} (Sisa Ikan: {{ number_format($kolam->jumlah) }} ekor)
                        </option>
                    @endforeach
                </select>
                @error('kolam_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Tanggal -->
                <div>
                    <label for="tanggal_penjualan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Panen <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_penjualan" id="tanggal_penjualan" value="{{ old('tanggal_penjualan', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                    @error('tanggal_penjualan') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Pembeli (Pelanggan) -->
                <div>
                    <label for="pelanggan_id" class="block text-sm font-medium text-gray-700 mb-1">Pelanggan <span class="text-red-500">*</span></label>
                    <div class="flex">
                        <select name="pelanggan_id" id="pelanggan_id" class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($pelanggans as $p)
                                <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ $p->tipe }})
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('pelanggan.create') }}" target="_blank" class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md text-gray-600 hover:bg-gray-200 text-sm flex items-center" title="Tambah Pelanggan Baru">
                            +
                        </a>
                    </div>
                    @error('pelanggan_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Jumlah Ekor -->
                <div>
                    <label for="jumlah_ekor" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Ekor <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_ekor" id="jumlah_ekor" value="{{ old('jumlah_ekor') }}" class="w-full bg-gray-50 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required readonly>
                    @error('jumlah_ekor') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Harga Per Ekor -->
                <div>
                    <label for="harga_per_ekor" class="block text-sm font-medium text-gray-700 mb-1">Harga per Ekor (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_per_ekor" id="harga_per_ekor" value="{{ old('harga_per_ekor') }}" step="50" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required oninput="calculateTotal()">
                    @error('harga_per_ekor') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="total_harga" class="block text-sm font-medium text-gray-700 mb-1">Total Penjualan (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="total_harga" id="total_harga" value="{{ old('total_harga') }}" class="w-full bg-gray-50 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" readonly>
            </div>

            <script>
                function calculateTotal() {
                    const ekor = parseFloat(document.getElementById('jumlah_ekor').value) || 0;
                    const harga = parseFloat(document.getElementById('harga_per_ekor').value) || 0;
                    document.getElementById('total_harga').value = ekor * harga;
                }
                
                document.getElementById('jumlah_ekor').addEventListener('input', calculateTotal);

                // Auto fill jumlah_ekor from selected pond's current fish count
                document.getElementById('kolam_id').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const jumlah = selectedOption.getAttribute('data-jumlah');
                    if (jumlah !== null && jumlah !== undefined) {
                        document.getElementById('jumlah_ekor').value = jumlah;
                        calculateTotal();
                    }
                });
            </script>

            <div class="flex justify-end space-x-3 pt-6 border-t mt-4">
                <a href="{{ route('penjualan.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium shadow-sm">Simpan & Panen</button>
            </div>
        </div>
    </form>
@endsection
