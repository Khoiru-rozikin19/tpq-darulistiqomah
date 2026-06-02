@extends('layouts.app')

@section('title', 'Kwitansi SPP')
@section('page_title', 'Kwitansi SPP Santri')

@section('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <!-- Back and Print actions (no-print) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 no-print bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
        <a href="{{ route('spp.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors">
            &larr; Kembali ke Riwayat
        </a>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5">
                <label for="petugas-select" class="text-xs font-semibold text-slate-500">Penandatangan:</label>
                <select id="petugas-select" onchange="updateSigner(this.value)" class="px-2 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-700 bg-slate-50 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @php
                        // Try to find default admin_tu pegawai
                        $defaultPetugas = $pegawais->firstWhere('jabatan', 'Admin TU') ?? $pegawais->first();
                    @endphp
                    @foreach ($pegawais as $peg)
                        <option value="{{ $peg->nama }}" {{ ($defaultPetugas && $defaultPetugas->id === $peg->id) ? 'selected' : '' }}>
                            {{ $peg->nama }} ({{ $peg->jabatan }})
                        </option>
                    @endforeach
                    @if ($pegawais->isEmpty())
                        <option value="{{ $payment->user->name }}">{{ $payment->user->name }} (User)</option>
                    @endif
                </select>
            </div>
            <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10">
                🖨️ Cetak Kwitansi
            </button>
        </div>
    </div>

    <!-- Kwitansi Card -->
    <div id="print-area" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 md:p-12 relative overflow-hidden">
        <!-- Stamp Background effect -->
        <div class="absolute right-8 bottom-8 w-40 h-40 border-8 border-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500/10 font-bold text-lg select-none transform rotate-12">
            LUNAS
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-6 mb-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Darul Istiqomah" class="w-10 h-10 object-contain rounded-xl">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 heading-font leading-tight">Darul Istiqomah</h3>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Madrasah Keuangan</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[9px] font-bold text-blue-600 uppercase tracking-wider block">Kwitansi SPP</span>
                <span class="text-xs font-semibold text-slate-700 block mt-1">#SPP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <!-- Details -->
        <div class="space-y-6 text-xs text-slate-600 mb-8">
            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-400 font-medium">Telah Diterima Dari</span>
                <span class="col-span-2 font-bold text-slate-800 text-sm">: {{ $payment->santri->nama }}</span>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-400 font-medium">Nomor Induk Santri</span>
                <span class="col-span-2 font-semibold text-slate-800">: {{ $payment->santri->nis }}</span>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-400 font-medium">Kelas / Kelompok</span>
                <span class="col-span-2 font-semibold text-slate-800">: {{ $payment->santri->kelas }}</span>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-400 font-medium">Untuk Pembayaran</span>
                <span class="col-span-2 font-bold text-blue-600">: SPP Bulan {{ $payment->nama_bulan }} (T.A. {{ $payment->tahun_ajaran }})</span>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <span class="text-slate-400 font-medium">Metode Pembayaran</span>
                <span class="col-span-2 font-semibold text-slate-800 uppercase">: {{ $payment->metode_bayar }}</span>
            </div>

            @if ($payment->keterangan)
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-slate-400 font-medium">Keterangan</span>
                    <span class="col-span-2 text-slate-500 italic">: "{{ $payment->keterangan }}"</span>
                </div>
            @endif
        </div>

        <!-- Nominal Block -->
        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="block text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Jumlah Nominal</span>
                <span class="text-xl font-extrabold text-slate-800 heading-font mt-1 block">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</span>
            </div>
            <div class="text-right">
                <span class="block text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Status Pembayaran</span>
                <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 font-bold rounded-xl text-[10px] mt-1 border border-emerald-100">
                    LUNAS
                </span>
            </div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-4 text-center text-xs mt-12 pt-8 border-t border-slate-100">
            <div>
                <span class="text-slate-400 block mb-12">Wali Santri</span>
                <span class="font-bold text-slate-700 block underline decoration-dotted">{{ $payment->santri->nama_wali }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Tanjung Makmur, {{ $payment->tanggal_bayar->locale('id')->translatedFormat('d F Y') }}</span>
                <span class="text-slate-400 block mb-10">Petugas Keuangan TU</span>
                <span id="signer-name" class="font-bold text-slate-700 block underline">{{ $defaultPetugas ? $defaultPetugas->nama : $payment->user->name }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateSigner(name) {
        document.getElementById('signer-name').innerText = name;
    }
</script>
@endsection
