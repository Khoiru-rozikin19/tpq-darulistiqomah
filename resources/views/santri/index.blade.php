@extends('layouts.app')

@section('title', 'Data Santri')
@section('page_title', 'Pengelolaan Data Santri')

@section('content')
<!-- Header Card -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800 heading-font">Daftar Santri Darul Istiqomah</h3>
            <p class="text-xs text-slate-400">Total santri terdaftar: {{ $santris->total() }} santri</p>
        </div>
        <a href="{{ route('santri.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Santri Baru
        </a>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('santri.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-50">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pencarian</label>
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIS, nama, alamat..." 
                    class="w-full pl-3 pr-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Status Keanggotaan</label>
            <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                <option value="">Semua Status</option>
                <option value="aktif" {{ $status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="alumni" {{ $status == 'alumni' ? 'selected' : '' }}>Alumni</option>
                <option value="keluar" {{ $status == 'keluar' ? 'selected' : '' }}>Keluar</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Kelas</label>
            <select name="kelas" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                <option value="">Semua Kelas</option>
                @foreach ($kelases as $kls)
                    <option value="{{ $kls }}" {{ $kelas == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs transition-all">
                Filter Data
            </button>
            @if ($search || $status || $kelas)
                <a href="{{ route('santri.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition-all text-center">
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
                    <th class="px-6 py-4">Foto / NIS</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Gender</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Wali Santri</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600">
                @forelse ($santris as $santri)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            <div class="flex items-center gap-3">
                                @if ($santri->foto)
                                    <img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto {{ $santri->nama }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                        {{ substr($santri->nama, 0, 1) }}
                                    </div>
                                @endif
                                <span>{{ $santri->nis }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800 text-sm">{{ $santri->nama }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $santri->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                {{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $santri->kelas }}</td>
                        <td class="px-6 py-4">
                            <span class="block font-medium text-slate-700">{{ $santri->nama_wali }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $santri->no_hp_wali ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($santri->status === 'aktif')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">Aktif</span>
                            @elseif ($santri->status === 'alumni')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">Alumni</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100 uppercase tracking-wider">Keluar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('santri.show', $santri->id) }}" class="p-2 bg-slate-50 hover:bg-blue-50 text-slate-500 hover:text-blue-600 rounded-xl transition-all" title="Detail Profil">
                                    👁️
                                </a>
                                <a href="{{ route('santri.edit', $santri->id) }}" class="p-2 bg-slate-50 hover:bg-amber-50 text-slate-500 hover:text-amber-600 rounded-xl transition-all" title="Edit Data">
                                    ✏️
                                </a>
                                <form action="{{ route('santri.destroy', $santri->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data santri ini? Semua riwayat SPP santri ini juga akan dihapus secara permanen.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-slate-50 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-xl transition-all" title="Hapus Data">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <span class="block text-2xl mb-2">🔍</span>
                            Tidak ada data santri ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if ($santris->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $santris->links() }}
        </div>
    @endif
</div>
@endsection
