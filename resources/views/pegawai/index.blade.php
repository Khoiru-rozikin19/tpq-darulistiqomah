@extends('layouts.app')

@section('title', 'Daftar Pegawai')
@section('page_title', 'Pengelolaan Daftar Pegawai')

@section('content')
<!-- Header Card -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800 heading-font">Daftar Pegawai Yayasan</h3>
            <p class="text-xs text-slate-400">Total pegawai aktif/terdaftar: {{ $pegawais->total() }} orang</p>
        </div>
        @if (Auth::user()->isAdminTU())
            <a href="{{ route('pegawai.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pegawai Baru
            </a>
        @endif
    </div>

    <!-- Filter Form -->
    <form action="{{ route('pegawai.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-50">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pencarian</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIP, nama, alamat..." 
                class="w-full pl-3 pr-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Jabatan</label>
            <select name="jabatan" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                <option value="">Semua Jabatan</option>
                @foreach ($jabatans as $jab)
                    <option value="{{ $jab }}" {{ $jabatan == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Status Keaktifan</label>
            <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                <option value="">Semua Status</option>
                <option value="aktif" {{ $status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="non_aktif" {{ $status == 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs transition-all">
                Filter Data
            </button>
            @if ($search || $status || $jabatan)
                <a href="{{ route('pegawai.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition-all text-center">
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
                    <th class="px-6 py-4">Nama / NIP</th>
                    <th class="px-6 py-4">Gender</th>
                    <th class="px-6 py-4">Jabatan</th>
                    <th class="px-6 py-4">No. HP</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600">
                @forelse ($pegawais as $pegawai)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                    {{ substr($pegawai->nama, 0, 1) }}
                                </div>
                                <div>
                                    <span class="block text-slate-800 font-bold text-sm">{{ $pegawai->nama }}</span>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">{{ $pegawai->nip ?: 'NIP: -' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $pegawai->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                {{ $pegawai->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700 text-sm">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200/50">
                                {{ $pegawai->jabatan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $pegawai->no_hp ?: '-' }}</td>
                        <td class="px-6 py-4">
                            @if ($pegawai->status === 'aktif')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100 uppercase tracking-wider">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('pegawai.show', $pegawai->id) }}" class="p-2 bg-slate-50 hover:bg-blue-50 text-slate-500 hover:text-blue-600 rounded-xl transition-all" title="Detail Pegawai">
                                    👁️
                                </a>
                                @if (Auth::user()->isAdminTU())
                                    <a href="{{ route('pegawai.edit', $pegawai->id) }}" class="p-2 bg-slate-50 hover:bg-amber-50 text-slate-500 hover:text-amber-600 rounded-xl transition-all" title="Edit Data">
                                        ✏️
                                    </a>
                                    <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-slate-50 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-xl transition-all" title="Hapus Data">
                                            🗑️
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <span class="block text-2xl mb-2">🔍</span>
                            Tidak ada data pegawai ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if ($pegawais->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $pegawais->links() }}
        </div>
    @endif
</div>
@endsection
