@extends('layouts.app')

@section('title', 'Catat Kas')
@section('page_title', 'Catat Transaksi KAS')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Back link -->
    <a href="{{ route('kas.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 mb-6 transition-colors">
        &larr; Kembali ke Buku Kas
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-blue-700 to-blue-800 text-white">
            <h3 class="text-lg font-bold heading-font">Catat Transaksi Kas Baru</h3>
            <p class="text-xs text-blue-100 mt-1">Masukkan data transaksi masuk (Pemasukan) atau keluar (Pengeluaran) operasional madrasah.</p>
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

        <form action="{{ route('kas.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Jenis Transaksi -->
                <div>
                    <label for="jenis" class="block text-xs font-semibold text-slate-500 mb-2">Jenis Transaksi</label>
                    <select name="jenis" id="jenis" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-bold">
                        <option value="masuk" {{ old('jenis') === 'masuk' ? 'selected' : '' }}>MASUK (Penerimaan / Hibah / Infaq)</option>
                        <option value="keluar" {{ old('jenis') === 'keluar' ? 'selected' : '' }}>KELUAR (Pengeluaran / Operasional / Belanja)</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-xs font-semibold text-slate-500 mb-2">Kategori Transaksi</label>
                    <input type="text" name="kategori" id="kategori" list="kategori-suggestions" value="{{ old('kategori') }}" required placeholder="Contoh: Infaq, Operasional, ATK..." 
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                    <datalist id="kategori-suggestions">
                        <option value="Infaq">
                        <option value="Dana Hibah">
                        <option value="ATK & Cetak">
                        <option value="Listrik & Air">
                        <option value="Bisyarah Guru">
                        <option value="Konsumsi">
                        <option value="Pemeliharaan Gedung">
                        <option value="Sumbangan Kegiatan">
                    </datalist>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-xs font-semibold text-slate-500 mb-2">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required 
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                </div>

                <!-- Nominal -->
                <div>
                    <label for="nominal" class="block text-xs font-semibold text-slate-500 mb-2">Nominal Transaksi (Rp)</label>
                    <input type="number" name="nominal" id="nominal" value="{{ old('nominal') }}" required placeholder="Masukkan jumlah nominal uang..." 
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-bold text-slate-800">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-xs font-semibold text-slate-500 mb-2">Deskripsi / Keterangan Transaksi</label>
                <textarea name="keterangan" id="keterangan" rows="4" required placeholder="Jelaskan detail tujuan transaksi ini..." 
                    class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('kas.index') }}" class="px-5 py-3 border border-slate-200 text-slate-500 hover:text-slate-800 font-semibold rounded-2xl text-xs transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
                    Simpan Transaksi Kas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
