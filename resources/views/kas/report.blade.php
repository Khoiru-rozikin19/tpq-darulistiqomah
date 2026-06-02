<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Kas Umum - Darul Istiqomah</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: white;
            color: #1e293b;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body class="p-8 md:p-16 max-w-4xl mx-auto">

    <!-- Print Control Bar (no-print) -->
    <div class="mb-8 p-4 bg-slate-50 border border-slate-100 rounded-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
        <div class="flex flex-wrap items-center gap-4 text-xs">
            <div class="flex items-center gap-1.5">
                <label for="kepala-select" class="font-semibold text-slate-500">Kepala Madrasah:</label>
                <select id="kepala-select" onchange="updateKepala(this.value)" class="px-2 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none">
                    @php
                        $defaultKepala = $pegawais->firstWhere('jabatan', 'Kepala Madrasah') ?? $pegawais->first();
                    @endphp
                    @foreach ($pegawais as $peg)
                        <option value="{{ $peg->nama }}" {{ ($defaultKepala && $defaultKepala->id === $peg->id) ? 'selected' : '' }}>
                            {{ $peg->nama }}
                        </option>
                    @endforeach
                    @if ($pegawais->isEmpty())
                        <option value="K.H. Rahmat">K.H. Rahmat</option>
                    @endif
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <label for="bendahara-select" class="font-semibold text-slate-500">Bendahara TU:</label>
                <select id="bendahara-select" onchange="updateBendahara(this.value)" class="px-2 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none">
                    @php
                        $defaultBendahara = $pegawais->firstWhere('jabatan', 'Admin TU') ?? $pegawais->firstWhere('jabatan', 'Bendahara') ?? ($pegawais->skip(1)->first() ?? $pegawais->first());
                    @endphp
                    @foreach ($pegawais as $peg)
                        <option value="{{ $peg->nama }}" {{ ($defaultBendahara && $defaultBendahara->id === $peg->id) ? 'selected' : '' }}>
                            {{ $peg->nama }}
                        </option>
                    @endforeach
                    @if ($pegawais->isEmpty())
                        <option value="{{ Auth::user()->name }}">{{ Auth::user()->name }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="window.close()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold rounded-xl text-xs transition-all">
                Tutup Halaman
            </button>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-all">
                Cetak Sekarang
            </button>
        </div>
    </div>

    <!-- Report Container -->
    <div>
        <!-- Kop Surat / Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-6 mb-8">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Darul Istiqomah" class="w-12 h-12 object-contain rounded-xl">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-950 heading-font leading-tight">MADRASAH DARUL ISTIQOMAH</h1>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sistem Pencatatan Keuangan & Kas Umum</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Alamat : Blok. K, Dusun. Darma Tama, Desa. Tanjung Makmur, Kec. Sinar Peninjauan, Kab. Ogan Komering Ulu</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-slate-900 block">LAPORAN BUKU KAS</span>
                <span class="text-[10px] text-slate-400 block mt-1">Dicetak: {{ date('d F Y H:i') }}</span>
            </div>
        </div>

        <!-- Filter Info -->
        <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                Periode Laporan: 
                @if ($tanggalMulai && $tanggalSelesai)
                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}
                @elseif ($tanggalMulai)
                    Sejak {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }}
                @elseif ($tanggalSelesai)
                    Sampai {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}
                @else
                    Semua Periode Transaksi
                @endif
            </h3>
            @if ($jenis || $kategori)
                <p class="text-xs text-slate-500 mt-1">
                    Filter Tambahan: 
                    @if($jenis) Jenis: <strong class="uppercase text-slate-700">{{ $jenis }}</strong>; @endif
                    @if($kategori) Kategori: <strong class="text-slate-700">{{ $kategori }}</strong>; @endif
                </p>
            @endif
        </div>

        <!-- Summary Boxes (Print-safe style) -->
        <div class="grid grid-cols-3 border border-slate-900 rounded-xl overflow-hidden mb-8 text-xs text-center bg-slate-50/50">
            <div class="p-4 border-r border-slate-900">
                <span class="block font-semibold text-slate-500 uppercase tracking-wider">Total Masuk</span>
                <span class="block font-bold text-emerald-700 text-sm mt-1">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
            </div>
            <div class="p-4 border-r border-slate-900">
                <span class="block font-semibold text-slate-500 uppercase tracking-wider">Total Keluar</span>
                <span class="block font-bold text-rose-700 text-sm mt-1">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
            </div>
            <div class="p-4 bg-slate-900 text-white">
                <span class="block font-semibold text-slate-400 uppercase tracking-wider">Saldo Periode</span>
                <span class="block font-extrabold text-base mt-0.5">Rp {{ number_format($saldoPeriode, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Ledger Table -->
        <table class="w-full text-left text-[11px] border border-slate-200">
            <thead>
                <tr class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300">
                    <th class="px-3 py-2 border-r border-slate-200">No.</th>
                    <th class="px-3 py-2 border-r border-slate-200">Tanggal</th>
                    <th class="px-3 py-2 border-r border-slate-200">Kategori</th>
                    <th class="px-3 py-2 border-r border-slate-200">Keterangan</th>
                    <th class="px-3 py-2 border-r border-slate-200 text-right">Pemasukan (Rp)</th>
                    <th class="px-3 py-2 text-right">Pengeluaran (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @php $runningBalance = 0; @endphp
                @forelse ($kasEntries as $index => $entry)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-3 py-2 border-r border-slate-200 text-center font-medium">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 border-r border-slate-200">{{ $entry->tanggal->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 border-r border-slate-200 font-semibold">{{ $entry->kategori }}</td>
                        <td class="px-3 py-2 border-r border-slate-200 text-slate-600 leading-normal">{{ $entry->keterangan }}</td>
                        <td class="px-3 py-2 border-r border-slate-200 text-right font-semibold text-emerald-600">
                            {{ $entry->jenis === 'masuk' ? number_format($entry->nominal, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-3 py-2 text-right font-semibold text-rose-600">
                            {{ $entry->jenis === 'keluar' ? number_format($entry->nominal, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-400">Tidak ada transaksi dalam periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-slate-100 font-bold border-t border-slate-300 text-slate-800">
                    <td colspan="4" class="px-3 py-2 border-r border-slate-200 text-right uppercase tracking-wider">Jumlah Total</td>
                    <td class="px-3 py-2 border-r border-slate-200 text-right text-emerald-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-right text-rose-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures (Tanda Tangan Pelaporan) -->
        <div class="grid grid-cols-2 gap-4 text-xs text-center mt-16 pt-8 border-t border-slate-100">
            <div>
                <span class="text-slate-400 block mb-16">Mengetahui, Kepala Madrasah</span>
                <span id="kepala-name" class="font-bold text-slate-800 block underline">{{ $defaultKepala ? $defaultKepala->nama : 'K.H. Rahmat' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Tanjung Makmur, {{ date('d F Y') }}</span>
                <span class="text-slate-400 block mb-14">Dibuat oleh, Bendahara TU</span>
                <span id="bendahara-name" class="font-bold text-slate-800 block underline">{{ $defaultBendahara ? $defaultBendahara->nama : Auth::user()->name }}</span>
            </div>
        </div>
    </div>

    <!-- Auto print logic and dynamic signature updater -->
    <script>
        window.addEventListener('load', () => {
            // Auto open print dialog
            setTimeout(() => {
                window.print();
            }, 300);
        });

        function updateKepala(name) {
            document.getElementById('kepala-name').innerText = name;
        }

        function updateBendahara(name) {
            document.getElementById('bendahara-name').innerText = name;
        }
    </script>
</body>
</html>
