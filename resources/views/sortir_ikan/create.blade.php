@extends('layouts.app')

@section('title', 'Input Sortir Ikan')

@section('content')
    <form action="{{ route('sortir-ikan.store') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Form Sortir Ikan</h2>

            <div class="space-y-4">
                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sortir</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                    @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kolam Asal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kolam Asal</label>
                    <select name="kolam_asal_id" id="kolam_asal_id" class="w-full rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500" required onchange="updateMaxQty()">
                        <option value="">-- Pilih Kolam Asal --</option>
                        @foreach($kolams as $k)
                            <option value="{{ $k->id }}" data-jumlah="{{ $k->jumlah }}" {{ old('kolam_asal_id') == $k->id ? 'selected' : '' }}>
                                Kolam #{{ $k->nomor_kolam }} (Stok: {{ $k->jumlah }}, Status: {{ $k->status }})
                            </option>
                        @endforeach
                    </select>
                    @error('kolam_asal_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kolam Tujuan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kolam Tujuan</label>
                    <select name="kolam_tujuan_id" class="w-full rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                        <option value="">-- Pilih Kolam Tujuan --</option>
                        @foreach($destKolams as $k)
                            <option value="{{ $k->id }}" {{ old('kolam_tujuan_id') == $k->id ? 'selected' : '' }}>
                                Kolam #{{ $k->nomor_kolam }} (Isi: {{ $k->jumlah }}, Status: {{ $k->status }})
                            </option>
                        @endforeach
                    </select>
                    @error('kolam_tujuan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Jumlah --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pindah</label>
                        <input type="number" name="jumlah" id="jumlah" min="1" class="w-full rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                        <p id="max_info" class="text-xs text-gray-500 mt-1"></p>
                        @error('jumlah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Ukuran --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Ikan Tujuan</label>
                        <input type="text" name="ukuran_ikan_tujuan" placeholder="Contoh: 7-9 cm" class="w-full rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500" required>
                        <p class="text-xs text-gray-500 mt-1">Ukuran di kolam tujuan akan diupdate.</p>
                        @error('ukuran_ikan_tujuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="2" class="w-full rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition shadow-sm font-medium">Simpan Data Sortir</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function updateMaxQty() {
            const select = document.getElementById('kolam_asal_id');
            const selectedOption = select.options[select.selectedIndex];
            const max = selectedOption.getAttribute('data-jumlah');
            const input = document.getElementById('jumlah');
            
            if (max) {
                input.max = max;
                document.getElementById('max_info').innerText = 'Maksimal: ' + max + ' ekor';
            } else {
                input.removeAttribute('max');
                document.getElementById('max_info').innerText = '';
            }
        }
    </script>
@endsection
