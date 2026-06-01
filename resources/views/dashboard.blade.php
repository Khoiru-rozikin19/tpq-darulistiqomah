@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Ringkasan')

@section('content')
<!-- Welcome banner -->
<div class="mb-8 p-6 bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 rounded-3xl text-white shadow-xl shadow-blue-500/10 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-2xl -mr-16 -mt-16"></div>
    <div class="z-10 relative">
        <h3 class="text-2xl font-bold heading-font">Selamat Datang di Portal Keuangan Madrasah Darul Istiqomah</h3>
        <p class="text-blue-100 text-sm mt-1">Kelola data santri, pembayaran SPP bulanan, dan arus transaksi KAS madrasah dengan transparan dan efisien.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Santri Aktif -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
            👥
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Santri Aktif</p>
            <h4 class="text-2xl font-extrabold text-slate-800 heading-font mt-1">{{ $totalSantriAktif }}</h4>
        </div>
    </div>

    <!-- Card 2: SPP Bulan Ini -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
            💰
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">SPP Bulan Ini</p>
            <h4 class="text-2xl font-extrabold text-slate-800 heading-font mt-1">Rp {{ number_format($totalSppBulanIni, 0, ',', '.') }}</h4>
        </div>
    </div>

    <!-- Card 3: Saldo KAS -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
            ⚖️
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saldo KAS</p>
            <h4 class="text-2xl font-extrabold text-slate-800 heading-font mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h4>
        </div>
    </div>

    <!-- Card 4: Belum Lunas SPP -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-all">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
            ⏳
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Belum Lunas SPP</p>
            <h4 class="text-2xl font-extrabold text-slate-800 heading-font mt-1">{{ $belumLunasCount }} <span class="text-xs font-normal text-slate-400">Santri</span></h4>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Chart: SPP Bulanan (2/3 width on large screens) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h4 class="text-base font-bold text-slate-800 heading-font">Statistik Pembayaran SPP</h4>
                <p class="text-xs text-slate-400">Penerimaan bulanan SPP santri selama 6 bulan terakhir</p>
            </div>
            <span class="text-xs bg-blue-50 text-blue-600 font-semibold px-3 py-1 rounded-xl">20000 / Bulan</span>
        </div>
        <div class="h-64 relative">
            <canvas id="sppChart"></canvas>
        </div>
    </div>

    <!-- Quick Info / Shortcut (1/3 width on large screens) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
        <div>
            <h4 class="text-base font-bold text-slate-800 heading-font mb-4">Aksi Cepat</h4>
            <div class="space-y-3">
                <a href="{{ route('santri.create') }}" class="flex items-center p-3 rounded-2xl border border-slate-100 hover:bg-blue-50/50 hover:border-blue-100 transition-all group">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold mr-3 group-hover:scale-105 transition-transform">➕</span>
                    <div>
                        <span class="block text-xs font-bold text-slate-700">Tambah Santri Baru</span>
                        <span class="block text-[10px] text-slate-400">Daftarkan santri baru ke madrasah</span>
                    </div>
                </a>

                <a href="{{ route('spp.create') }}" class="flex items-center p-3 rounded-2xl border border-slate-100 hover:bg-emerald-50/50 hover:border-emerald-100 transition-all group">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mr-3 group-hover:scale-105 transition-transform font-sans">Rp</span>
                    <div>
                        <span class="block text-xs font-bold text-slate-700">Catat Bayar SPP</span>
                        <span class="block text-[10px] text-slate-400">Pembayaran SPP bulanan santri</span>
                    </div>
                </a>

                <a href="{{ route('kas.create') }}" class="flex items-center p-3 rounded-2xl border border-slate-100 hover:bg-amber-50/50 hover:border-amber-100 transition-all group">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold mr-3 group-hover:scale-105 transition-transform font-sans">⚖️</span>
                    <div>
                        <span class="block text-xs font-bold text-slate-700">Catat Transaksi KAS</span>
                        <span class="block text-[10px] text-slate-400">Uang masuk / pengeluaran operasional</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-slate-100 bg-slate-50/50 -mx-6 -mb-6 p-6 rounded-b-3xl">
            <h5 class="text-xs font-bold text-slate-700 mb-1">Tahun Ajaran Aktif</h5>
            <p class="text-sm font-semibold text-blue-600">{{ $tahunAjaran }}</p>
        </div>
    </div>
</div>

<!-- Recent Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Table 1: Recent SPP Payments -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-bold text-slate-800 heading-font">Pembayaran SPP Terakhir</h4>
            <a href="{{ route('spp.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100">
                        <th class="pb-3">Tanggal</th>
                        <th class="pb-3">Santri</th>
                        <th class="pb-3">Bulan</th>
                        <th class="pb-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($recentSpp as $spp)
                        <tr class="text-slate-600 hover:bg-slate-50/50 transition-colors">
                            <td class="py-3">{{ $spp->tanggal_bayar->format('d/m/Y') }}</td>
                            <td class="py-3 font-semibold text-slate-800">{{ $spp->santri->nama }}</td>
                            <td class="py-3">{{ $spp->nama_bulan }} ({{ $spp->tahun_ajaran }})</td>
                            <td class="py-3 text-right font-semibold text-emerald-600">Rp {{ number_format($spp->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">Belum ada pembayaran SPP dicatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table 2: Recent KAS Transactions -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-bold text-slate-800 heading-font">Transaksi KAS Terakhir</h4>
            <a href="{{ route('kas.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100">
                        <th class="pb-3">Tanggal</th>
                        <th class="pb-3">Kategori</th>
                        <th class="pb-3">Keterangan</th>
                        <th class="pb-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($recentKas as $kas)
                        <tr class="text-slate-600 hover:bg-slate-50/50 transition-colors">
                            <td class="py-3">{{ $kas->tanggal->format('d/m/Y') }}</td>
                            <td class="py-3">
                                <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $kas->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                    {{ $kas->kategori }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-700 max-w-[150px] truncate" title="{{ $kas->keterangan }}">{{ $kas->keterangan }}</td>
                            <td class="py-3 text-right font-semibold {{ $kas->jenis === 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">Belum ada transaksi KAS dicatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('sppChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartData) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penerimaan SPP (Rp)',
                    data: data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value, index, values) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
