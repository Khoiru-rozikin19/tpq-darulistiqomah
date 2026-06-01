@extends('layouts.app')

@section('title', 'Pembayaran SPP')
@section('page_title', 'Arus Kas SPP')

@section('content')
<!-- Header Card -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800 heading-font">Riwayat Pembayaran SPP</h3>
            <p class="text-xs text-slate-400">Total pencatatan transaksi SPP: {{ $payments->total() }} pembayaran</p>
        </div>
        <a href="{{ route('spp.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
            💸 Catat SPP Masuk
        </a>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('spp.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-50">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Cari Santri</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NIS..." 
                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Bulan Pembayaran</label>
            <select name="bulan" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                <option value="">Semua Bulan</option>
                @foreach([
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ] as $val => $name)
                    <option value="{{ $val }}" {{ $bulan == $val ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tahun Ajaran</label>
            <select name="tahun_ajaran" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                <option value="">Semua Tahun Ajaran</option>
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs transition-all">
                Filter Transaksi
            </button>
            @if ($search || $bulan || $tahunAjaran)
                <a href="{{ route('spp.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition-all text-center">
                    Reset
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
                    <th class="px-6 py-4">Tanggal Bayar</th>
                    <th class="px-6 py-4">Santri (NIS)</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Untuk Bulan</th>
                    <th class="px-6 py-4">Metode</th>
                    <th class="px-6 py-4">Nominal</th>
                    <th class="px-6 py-4">Petugas TU</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $payment->tanggal_bayar->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('santri.show', $payment->santri->id) }}" class="block font-semibold text-slate-800 hover:text-blue-600 hover:underline">
                                {{ $payment->santri->nama }}
                            </a>
                            <span class="block text-[10px] text-slate-400 mt-0.5">NIS: {{ $payment->santri->nis }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $payment->santri->kelas }}</td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-700">{{ $payment->nama_bulan }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">T.A. {{ $payment->tahun_ajaran }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $payment->metode_bayar === 'tunai' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                {{ $payment->metode_bayar }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 font-medium">{{ $payment->user->name }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('spp.show', $payment->id) }}" class="p-2 bg-slate-50 hover:bg-blue-50 text-slate-500 hover:text-blue-600 rounded-xl transition-all" title="Lihat Kwitansi">
                                    📄
                                </a>
                                <form action="{{ route('spp.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pembayaran SPP ini? Saldo Kas masuk terkait juga akan dikurangi.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-slate-50 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-xl transition-all" title="Hapus Transaksi">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <span class="block text-2xl mb-2">🔍</span>
                            Belum ada riwayat pembayaran SPP ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($payments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
