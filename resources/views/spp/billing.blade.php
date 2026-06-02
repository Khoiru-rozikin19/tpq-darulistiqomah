@extends('layouts.app')

@section('title', 'Tagihan SPP')
@section('page_title', 'Tagihan SPP via WhatsApp')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Back to SPP history -->
    <a href="{{ route('spp.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors">
        &larr; Kembali ke Riwayat Pembayaran
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- KOLOM KIRI: Pengaturan & Template (4/12) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- 1. Form Filter Periode -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 heading-font mb-4 flex items-center gap-2">
                    <span>📅</span> Pilih Periode Tagihan
                </h3>
                <form action="{{ route('spp.billing') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Bulan</label>
                            <select name="bulan" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                                @foreach($namaBulanList as $val => $name)
                                    <option value="{{ $val }}" {{ $bulan == $val ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tahun Ajaran</label>
                            <select name="tahun_ajaran" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                                @foreach ($tahunAjarans as $ta)
                                    <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if ($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                </form>
            </div>

            <!-- 2. Template Editor & WhatsApp Preview -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 heading-font mb-1 flex items-center gap-2">
                        <span>💬</span> Template Pesan Tagihan
                    </h3>
                    <p class="text-[10px] text-slate-400">Sesuaikan isi pesan pengingat tagihan SPP di bawah ini.</p>
                </div>

                <!-- Input Nominal -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nominal Tagihan (Rp)</label>
                    <input type="number" id="nominal-spp" value="20000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-bold text-emerald-600 bg-slate-50/50" placeholder="20000">
                </div>

                <!-- Text Area Editor -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Pesan Template</label>
                    <textarea id="template-text" rows="8" class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 font-mono leading-relaxed">Assalamu'alaikum Wr. Wb.

Menginfokan kepada Bapak/Ibu *{nama_wali}* selaku wali dari santri *{nama_santri}* (NIS: {nis}), bahwa pembayaran SPP untuk bulan *{bulan}* (Tahun Ajaran: *{tahun_ajaran}*) sebesar *{nominal}* belum tercatat di administrasi kami.

Pembayaran dapat dilakukan melalui transfer bank atau langsung tunai ke Kantor TU Madrasah Darul Istiqomah.

Bila sudah melakukan pembayaran, mohon abaikan pesan ini atau kirimkan foto bukti transfer kepada kami. Terima kasih.

Wassalamu'alaikum Wr. Wb.
- Admin TU Darul Istiqomah</textarea>
                </div>

                <!-- Placeholders Info -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-2">Variabel yang Tersedia:</span>
                    <div class="flex flex-wrap gap-1.5">
                        <button onclick="insertPlaceholder('{nama_santri}')" class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-semibold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-colors">{nama_santri}</button>
                        <button onclick="insertPlaceholder('{nis}')" class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-semibold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-colors">{nis}</button>
                        <button onclick="insertPlaceholder('{kelas}')" class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-semibold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-colors">{kelas}</button>
                        <button onclick="insertPlaceholder('{nama_wali}')" class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-semibold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-colors">{nama_wali}</button>
                        <button onclick="insertPlaceholder('{bulan}')" class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-semibold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-colors">{bulan}</button>
                        <button onclick="insertPlaceholder('{tahun_ajaran}')" class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-semibold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-colors">{tahun_ajaran}</button>
                        <button onclick="insertPlaceholder('{nominal}')" class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-semibold text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-colors">{nominal}</button>
                    </div>
                </div>

                <!-- Preview WhatsApp Mockup (WOW Factor Design) -->
                <div class="border border-slate-100 rounded-3xl overflow-hidden shadow-md">
                    <!-- WhatsApp Header Mockup -->
                    <div class="bg-[#075e54] px-4 py-3 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs select-none">
                                💬
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Preview Tagihan</h4>
                                <span class="text-[9px] text-teal-100">Wali Santri (Online)</span>
                            </div>
                        </div>
                        <div class="flex gap-3 text-teal-100 text-sm">
                            <span>📞</span>
                            <span>📎</span>
                            <span>⋮</span>
                        </div>
                    </div>
                    <!-- WhatsApp Chat Area Mockup -->
                    <div class="bg-[#efeae2] p-4 min-h-[160px] flex flex-col justify-end relative" style="background-image: radial-gradient(circle, #dfdcd6 1px, transparent 1px); background-size: 10px 10px;">
                        <div class="bg-[#d9fdd3] text-slate-800 text-[11px] p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[90%] self-end relative border border-emerald-100 leading-relaxed">
                            <div id="preview-box" class="whitespace-pre-wrap">Loading preview...</div>
                            <span class="block text-right text-[8px] text-slate-400 mt-1">12:00 ✔✔</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Daftar Santri Belum Bayar (7/12) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full">
                <!-- Header Daftar & Search -->
                <div class="p-6 border-b border-slate-100 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 heading-font">Santri Belum Membayar SPP</h3>
                            <p class="text-xs text-slate-400">Daftar santri aktif yang belum melunasi SPP bulan <span class="font-semibold text-slate-600">{{ $namaBulanList[$bulan] }} {{ $tahunAjaran }}</span>.</p>
                        </div>
                        <span class="px-3 py-1.5 bg-rose-50 text-rose-600 font-bold rounded-2xl text-xs border border-rose-100 self-start sm:self-center">
                            {{ count($santris) }} Belum Bayar
                        </span>
                    </div>

                    <!-- Search Input -->
                    <form action="{{ route('spp.billing') }}" method="GET" class="flex gap-2">
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama santri, NIS, atau kelas..." 
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                        <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs transition-all">
                            Cari
                        </button>
                        @if ($search)
                            <a href="{{ route('spp.billing', ['bulan' => $bulan, 'tahun_ajaran' => $tahunAjaran]) }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition-all flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Tabel Santri -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="px-6 py-4">Santri</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4">Nama Wali</th>
                                <th class="px-6 py-4">Kontak Wali</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse ($santris as $s)
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <!-- Santri Name -->
                                    <td class="px-6 py-4">
                                        <a href="{{ route('santri.show', $s->id) }}" class="block font-semibold text-slate-800 hover:text-blue-600 hover:underline">
                                            {{ $s->nama }}
                                        </a>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">NIS: {{ $s->nis }}</span>
                                    </td>
                                    
                                    <!-- Kelas -->
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg font-bold text-[10px] border border-blue-100">
                                            {{ $s->kelas }}
                                        </span>
                                    </td>

                                    <!-- Wali -->
                                    <td class="px-6 py-4 font-semibold text-slate-700">
                                        {{ $s->nama_wali }}
                                    </td>

                                    <!-- Phone -->
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-slate-500 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100 text-[10px]">
                                            {{ $s->no_hp_wali ?: '-' }}
                                        </span>
                                    </td>

                                    <!-- Send Action -->
                                    <td class="px-6 py-4 text-center">
                                        @if ($s->no_hp_wali)
                                            <button onclick="sendBilling('{{ addslashes($s->nama) }}', '{{ $s->nis }}', '{{ $s->kelas }}', '{{ addslashes($s->nama_wali) }}', '{{ $s->no_hp_wali }}')" 
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[10px] transition-all hover:shadow-md hover:shadow-emerald-500/10 active:scale-[0.97]">
                                                <span>📲</span> Kirim Tagihan
                                            </button>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">No HP tidak valid</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <span class="block text-2xl mb-2">✨</span>
                                        Semua santri aktif telah melunasi SPP pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Config Variables dari backend
    const currentBulan = @json($namaBulanList[$bulan]);
    const currentTahunAjaran = @json($tahunAjaran);

    // Dapatkan elemen
    const templateTextarea = document.getElementById('template-text');
    const previewBox = document.getElementById('preview-box');
    const nominalInput = document.getElementById('nominal-spp');

    // Update live preview
    function updateLivePreview() {
        let text = templateTextarea.value;
        
        // Ganti variabel dengan data contoh agar preview terlihat bagus
        const exampleData = {
            '{nama_santri}': 'Jhonatan Arsa',
            '{nis}': '10001',
            '{kelas}': 'IBT 1',
            '{nama_wali}': 'Bp. Supardi',
            '{nominal}': formatRupiah(nominalInput.value),
            '{bulan}': currentBulan,
            '{tahun_ajaran}': currentTahunAjaran
        };

        for (let key in exampleData) {
            text = text.replace(new RegExp(escapeRegExp(key), 'g'), exampleData[key]);
        }

        // Tampilkan dengan format tebal sederhana (*text* -> bold)
        let formattedText = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');
        previewBox.innerHTML = formattedText;
    }

    // Insert placeholder di cursor
    function insertPlaceholder(placeholder) {
        const textarea = templateTextarea;
        const startPos = textarea.selectionStart;
        const endPos = textarea.selectionEnd;
        const text = textarea.value;
        
        textarea.value = text.substring(0, startPos) + placeholder + text.substring(endPos, text.length);
        textarea.focus();
        textarea.selectionStart = startPos + placeholder.length;
        textarea.selectionEnd = startPos + placeholder.length;
        
        updateLivePreview();
    }

    // Helper formatting & regex
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function formatRupiah(value) {
        if (!value) return 'Rp 0';
        return 'Rp ' + parseInt(value).toLocaleString('id-ID');
    }

    // Kirim tagihan WhatsApp
    function sendBilling(namaSantri, nis, kelas, namaWali, phone) {
        let text = templateTextarea.value;
        const nominal = nominalInput.value || '20000';

        // Ganti dengan data asli
        const realData = {
            '{nama_santri}': namaSantri,
            '{nis}': nis,
            '{kelas}': kelas,
            '{nama_wali}': namaWali,
            '{nominal}': formatRupiah(nominal),
            '{bulan}': currentBulan,
            '{tahun_ajaran}': currentTahunAjaran
        };

        for (let key in realData) {
            text = text.replace(new RegExp(escapeRegExp(key), 'g'), realData[key]);
        }

        // Bersihkan & format nomor HP (e.g. 08123 -> 628123)
        let cleanPhone = phone.replace(/[^0-9]/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.substring(1);
        } else if (!cleanPhone.startsWith('62')) {
            cleanPhone = '62' + cleanPhone; // Fallback default Indonesia
        }

        // Buka chat WhatsApp
        const waUrl = 'https://api.whatsapp.com/send?phone=' + cleanPhone + '&text=' + encodeURIComponent(text);
        window.open(waUrl, '_blank');
    }

    // Bind event
    templateTextarea.addEventListener('input', updateLivePreview);
    nominalInput.addEventListener('input', updateLivePreview);

    // Initial load
    document.addEventListener('DOMContentLoaded', updateLivePreview);
</script>
@endsection
