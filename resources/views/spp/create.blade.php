@extends('layouts.app')

@section('title', 'Catat SPP')
@section('page_title', 'Catat Pembayaran SPP')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Back link -->
    <a href="{{ route('spp.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 mb-6 transition-colors">
        &larr; Kembali ke Riwayat Pembayaran
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-blue-700 to-blue-800 text-white">
            <h3 class="text-lg font-bold heading-font">Catat SPP Masuk</h3>
            <p class="text-xs text-blue-100 mt-1">Gunakan form ini untuk mencatat penerimaan uang SPP bulanan santri.</p>
        </div>

        @if ($errors->any())
            <div class="p-6 bg-red-50 border-b border-red-100 text-red-700 text-xs">
                <p class="font-bold mb-2">Terjadi kesalahan input:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('spp.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf

            <!-- 1. Santri Selection -->
            <div>
                <label for="santri_id" class="block text-xs font-semibold text-slate-500 mb-2">Pilih Santri (Aktif)</label>
                
                <!-- Custom Searchable Dropdown -->
                <div class="relative" id="custom-select-container">
                    <button type="button" id="custom-select-trigger" class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-medium text-left flex justify-between items-center transition-all hover:border-slate-300">
                        <span id="custom-select-label" class="truncate">-- Pilih Santri --</span>
                        <svg class="w-4 h-4 text-slate-400 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <!-- Dropdown Content (Hidden by default) -->
                    <div id="custom-select-dropdown" class="hidden absolute left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 max-h-64 flex flex-col overflow-hidden">
                        <!-- Search Input -->
                        <div class="p-2 border-b border-slate-100 bg-slate-50/50">
                            <input type="text" id="custom-select-search" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-white" placeholder="Ketik nama/NIS/kelas untuk mencari...">
                        </div>
                        
                        <!-- Options List -->
                        <div class="overflow-y-auto flex-1 max-h-48" id="custom-select-options">
                            <div class="px-4 py-2.5 text-xs text-slate-400 hover:bg-slate-50 cursor-pointer custom-option" data-value="">-- Pilih Santri --</div>
                            @foreach ($santris as $s)
                                <div class="px-4 py-2.5 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 cursor-pointer custom-option {{ $santriId == $s->id ? 'bg-blue-50 text-blue-700 font-semibold' : '' }}" 
                                     data-value="{{ $s->id }}" 
                                     data-search-text="{{ strtolower($s->nama . ' ' . $s->nis . ' ' . $s->kelas) }}">
                                    {{ $s->nama }} (NIS: {{ $s->nis }} - Kelas {{ $s->kelas }})
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Hidden Input for Laravel -->
                <input type="hidden" name="santri_id" id="santri_id" value="{{ old('santri_id', $santriId) }}" required>
            </div>

            <!-- 2. Periode Pembayaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="bulan" class="block text-xs font-semibold text-slate-500 mb-2">Untuk Bulan</label>
                    <select name="bulan" id="bulan" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                        @foreach([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ] as $val => $name)
                            <option value="{{ $val }}" {{ $bulanSelected == $val ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tahun_ajaran" class="block text-xs font-semibold text-slate-500 mb-2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaranSelected) }}" required placeholder="Contoh: 2025/2026" 
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                </div>
            </div>

            <!-- 3. Rincian Transaksi -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label for="nominal" class="block text-xs font-semibold text-slate-500 mb-2">Nominal Pembayaran (Rp)</label>
                    <input type="number" name="nominal" id="nominal" value="{{ old('nominal', 20000) }}" required 
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-bold text-emerald-600">
                    <p class="text-[9px] text-slate-400 mt-1">Biaya SPP standar adalah Rp 20.000</p>
                </div>

                <div>
                    <label for="metode_bayar" class="block text-xs font-semibold text-slate-500 mb-2">Metode</label>
                    <select name="metode_bayar" id="metode_bayar" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                        <option value="tunai" {{ old('metode_bayar') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('metode_bayar') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal_bayar" class="block text-xs font-semibold text-slate-500 mb-2">Tanggal Pembayaran</label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required 
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                </div>

                <div>
                    <label for="keterangan" class="block text-xs font-semibold text-slate-500 mb-2">Keterangan Tambahan</label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional (misal: Lunas awal tahun)" 
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('spp.index') }}" class="px-5 py-3 border border-slate-200 text-slate-500 hover:text-slate-800 font-semibold rounded-2xl text-xs transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
                    Simpan & Catat Kas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('custom-select-container');
    const trigger = document.getElementById('custom-select-trigger');
    const label = document.getElementById('custom-select-label');
    const dropdown = document.getElementById('custom-select-dropdown');
    const searchInput = document.getElementById('custom-select-search');
    const optionsContainer = document.getElementById('custom-select-options');
    const hiddenInput = document.getElementById('santri_id');
    const options = Array.from(optionsContainer.getElementsByClassName('custom-option'));

    // Set initial label jika ada nilai terpilih
    const selectedOption = options.find(opt => opt.getAttribute('data-value') === hiddenInput.value);
    if (selectedOption) {
        label.textContent = selectedOption.textContent.trim();
    }

    // Toggle dropdown
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            searchInput.focus();
        }
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Filter pencarian
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase().trim();

        options.forEach(option => {
            const searchText = option.getAttribute('data-search-text') || '';
            const isDefault = option.getAttribute('data-value') === '';

            if (isDefault) {
                option.style.display = 'block';
            } else if (searchText.includes(query)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Pilihan opsi
    optionsContainer.addEventListener('click', function(e) {
        const option = e.target.closest('.custom-option');
        if (!option) return;

        const val = option.getAttribute('data-value');
        const text = option.textContent.trim();

        hiddenInput.value = val;
        label.textContent = text;
        dropdown.classList.add('hidden');

        // Reset pencarian dan tampilkan semua opsi kembali
        searchInput.value = '';
        options.forEach(opt => opt.style.display = 'block');

        // Update style aktif
        options.forEach(opt => {
            opt.classList.remove('bg-blue-50', 'text-blue-700', 'font-semibold');
        });
        if (val !== '') {
            option.classList.add('bg-blue-50', 'text-blue-700', 'font-semibold');
        }
    });
});
</script>
@endsection
