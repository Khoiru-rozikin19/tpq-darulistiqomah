@extends('layouts.app')

@section('title', 'Detail Santri')
@section('page_title', 'Profil Santri')

@section('content')
<div>
    <!-- Back link -->
    <a href="{{ route('santri.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 mb-6 transition-colors">
        &larr; Kembali ke Daftar Santri
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Avatar & Quick Actions -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-center">
                <!-- Avatar -->
                <div class="mx-auto w-24 h-24 rounded-3xl overflow-hidden bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-3xl mb-4 border-2 border-slate-100">
                    @if ($santri->foto)
                        <img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto {{ $santri->nama }}" class="w-full h-full object-cover">
                    @else
                        {{ substr($santri->nama, 0, 1) }}
                    @endif
                </div>

                <h3 class="text-lg font-bold text-slate-800 heading-font leading-snug">{{ $santri->nama }}</h3>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">NIS: {{ $santri->nis }}</p>
                
                <!-- Status Badge -->
                <div class="mt-3">
                    @if ($santri->status === 'aktif')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">Aktif</span>
                    @elseif ($santri->status === 'alumni')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">Alumni</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100 uppercase tracking-wider">Keluar</span>
                    @endif
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-50 text-left">
                    <div>
                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Kelas</span>
                        <span class="block text-sm font-bold text-slate-700 mt-0.5">{{ $santri->kelas }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Tahun Masuk</span>
                        <span class="block text-sm font-bold text-slate-700 mt-0.5">{{ $santri->tahun_masuk }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="text-xs font-bold text-slate-800 heading-font mb-4 uppercase tracking-wider">Tindakan Cepat</h4>
                <div class="space-y-2">
                    <a href="{{ route('spp.create', ['santri_id' => $santri->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-2xl text-xs transition-all heading-font">
                        💸 Bayar SPP Santri
                    </a>
                    <a href="{{ route('santri.edit', $santri->id) }}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-2xl text-xs transition-all heading-font">
                        ✏️ Edit Profil Santri
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column: Detail Information & SPP Status -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Details List Card -->
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="text-base font-bold text-slate-800 heading-font mb-6 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span>📋</span> Data Lengkap Santri
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div>
                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tempat, Tanggal Lahir</span>
                        <span class="text-sm font-medium text-slate-800">{{ $santri->tempat_lahir }}, {{ $santri->tanggal_lahir->format('d F Y') }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin</span>
                        <span class="text-sm font-medium text-slate-800">{{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>

                    <div class="md:col-span-2">
                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Alamat Domisili</span>
                        <span class="text-sm font-medium text-slate-800 leading-relaxed">{{ $santri->alamat }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Nama Wali / Orang Tua</span>
                        <span class="text-sm font-bold text-slate-800">{{ $santri->nama_wali }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Kontak Wali (HP)</span>
                        <span class="text-sm font-medium text-slate-800">{{ $santri->no_hp_wali ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- SPP Payment Status Card -->
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="text-base font-bold text-slate-800 heading-font mb-6 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span>💵</span> Status Pembayaran SPP (Rp 20.000 / Bulan)
                </h4>

                <!-- Grouped by Academic Year -->
                @forelse ($paymentsByYear as $year => $yearPayments)
                    <div class="mb-6 last:mb-0">
                        <h5 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 bg-blue-50 px-3.5 py-1.5 rounded-xl inline-block">
                            Tahun Ajaran {{ $year }}
                        </h5>

                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $payment = $yearPayments->firstWhere('bulan', $month);
                                    $namaBulan = [
                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                                        7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                                    ][$month];
                                @endphp
                                
                                @if ($payment)
                                    <!-- LUNAS -->
                                    <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-center relative group cursor-help" title="Dibayar tanggal {{ $payment->tanggal_bayar->format('d/m/Y') }} via {{ $payment->metode_bayar }}">
                                        <span class="block text-[10px] font-semibold text-emerald-400 uppercase">{{ $namaBulan }}</span>
                                        <span class="block text-xs font-bold text-emerald-700 mt-1">LUNAS</span>
                                        <span class="block text-[8px] text-emerald-500/80 font-semibold mt-0.5">{{ $payment->tanggal_bayar->format('d/m/y') }}</span>
                                    </div>
                                @else
                                    <!-- BELUM LUNAS -->
                                    <div class="p-3 bg-rose-50/50 border border-rose-100/50 rounded-2xl text-center cursor-pointer hover:bg-rose-50 transition-colors"
                                         onclick="window.location.href='{{ route('spp.create', ['santri_id' => $santri->id, 'bulan' => $month, 'tahun_ajaran' => $year]) }}'">
                                        <span class="block text-[10px] font-semibold text-rose-400/80 uppercase">{{ $namaBulan }}</span>
                                        <span class="block text-xs font-bold text-rose-600/80 mt-1">BELUM</span>
                                        <span class="block text-[8px] text-rose-400/60 font-semibold mt-0.5">Klik bayar</span>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                @empty
                    <!-- No payments at all -->
                    <div class="text-center py-10 border-2 border-dashed border-slate-100 rounded-3xl">
                        <span class="block text-2xl mb-2">💸</span>
                        <p class="text-xs text-slate-400">Belum ada riwayat pembayaran SPP untuk santri ini.</p>
                        <a href="{{ route('spp.create', ['santri_id' => $santri->id]) }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:underline mt-2">
                            Mulai Catat Pembayaran &rarr;
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
