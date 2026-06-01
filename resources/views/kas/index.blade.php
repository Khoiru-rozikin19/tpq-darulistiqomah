@extends('layouts.app')

@section('title', 'Transaksi KAS')
@section('page_title', 'Buku Kas Umum')

@section('content')
<!-- Top Stats Card (Balance Summary) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-700 to-indigo-900 p-6 rounded-3xl text-white shadow-lg shadow-blue-500/10">
        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Total Saldo Kas Saat Ini</span>
        <h3 class="text-2xl font-extrabold heading-font mt-1">Rp {{ number_format($overallSaldo, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-blue-200 mt-2">Saldo akhir terakumulasi dari awal pencatatan</p>
    </div>
    
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pemasukan (Filter)</span>
        <h3 class="text-xl font-extrabold text-emerald-600 heading-font mt-1">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-slate-400 mt-2">Jumlah uang masuk dalam periode pencarian</p>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pengeluaran (Filter)</span>
        <h3 class="text-xl font-extrabold text-rose-600 heading-font mt-1">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-slate-400 mt-2">Jumlah uang keluar dalam periode pencarian</p>
    </div>
</div>

<!-- Filter & Actions Card -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-50 pb-6 mb-6">
        <div>
            <h3 class="text-base font-bold text-slate-800 heading-font">Arus Transaksi Kas</h3>
            <p class="text-xs text-slate-400">Pencatatan pemasukan eksternal (Infaq, Sumbangan) & biaya operasional madrasah</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="printReport()" class="inline-flex items-center justify-center px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-all">
                🖨️ Cetak Laporan
            </button>
            <a href="{{ route('kas.create') }}" class="inline-flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
                ➕ Catat Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form id="filter-form" action="{{ route('kas.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Keterangan</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari deskripsi..." 
                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Jenis Kas</label>
            <select name="jenis" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                <option value="">Semua Jenis</option>
                <option value="masuk" {{ $jenis == 'masuk' ? 'selected' : '' }}>Masuk (Pemasukan)</option>
                <option value="keluar" {{ $jenis == 'keluar' ? 'selected' : '' }}>Keluar (Pengeluaran)</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Kategori</label>
            <select name="kategori" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                <option value="">Semua Kategori</option>
                @foreach ($kategories as $kat)
                    <option value="{{ $kat }}" {{ $kategori == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Mulai Tanggal</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" 
                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
        </div>

        <div class="flex items-end gap-2">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
            </div>
            
            <button type="submit" class="py-2.5 px-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs transition-all self-end" title="Terapkan Filter">
                Cari
            </button>
            @if ($search || $jenis || $kategori || $tanggalMulai || $tanggalSelesai)
                <a href="{{ route('kas.index') }}" class="py-2.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition-all text-center self-end" title="Reset Filter">
                    &times;
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Jenis</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4">Petugas</th>
                    <th class="px-6 py-4 text-right">Nominal</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600">
                @forelse ($kasEntries as $entry)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $entry->tanggal->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $entry->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                {{ $entry->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700">{{ $entry->kategori }}</td>
                        <td class="px-6 py-4 text-slate-500 max-w-[280px]" title="{{ $entry->keterangan }}">{{ $entry->keterangan }}</td>
                        <td class="px-6 py-4">{{ $entry->user->name }}</td>
                        <td class="px-6 py-4 text-right font-bold text-sm {{ $entry->jenis === 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $entry->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($entry->nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($entry->kategori !== 'SPP')
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('kas.edit', $entry->id) }}" class="p-2 bg-slate-50 hover:bg-amber-50 text-slate-500 hover:text-amber-600 rounded-xl transition-all" title="Edit Transaksi">
                                        ✏️
                                    </a>
                                    <form action="{{ route('kas.destroy', $entry->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan transaksi KAS ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-slate-50 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-xl transition-all" title="Hapus Transaksi">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 italic font-medium" title="Ubah data ini di menu Pembayaran SPP">Otomatis SPP</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <span class="block text-2xl mb-2">🔍</span>
                            Belum ada transaksi KAS dicatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($kasEntries->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $kasEntries->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function printReport() {
        const form = document.getElementById('filter-form');
        const params = new URLSearchParams(new FormData(form));
        params.set('print', 'true');
        
        const reportUrl = "{{ route('kas.index') }}?" + params.toString();
        window.open(reportUrl, '_blank');
    }
</script>
@endsection
