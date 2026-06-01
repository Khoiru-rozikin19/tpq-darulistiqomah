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
                <select name="santri_id" id="santri_id" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-medium">
                    <option value="">-- Pilih Santri --</option>
                    @foreach ($santris as $s)
                        <option value="{{ $s->id }}" {{ $santriId == $s->id ? 'selected' : '' }}>
                            {{ $s->nama }} (NIS: {{ $s->nis }} - Kelas {{ $s->kelas }})
                        </option>
                    @endforeach
                </select>
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
