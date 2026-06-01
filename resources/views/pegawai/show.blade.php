@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('page_title', 'Profil Detail Pegawai')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back link -->
    <a href="{{ route('pegawai.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 mb-6 transition-colors">
        &larr; Kembali ke Daftar Pegawai
    </a>

    <!-- Detail Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <!-- Profile Banner -->
        <div class="h-32 bg-gradient-to-r from-blue-700 to-indigo-800 relative">
            <div class="absolute -bottom-10 left-8">
                <div class="w-20 h-20 rounded-2xl bg-white p-1 shadow-md">
                    <div class="w-full h-full rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-2xl heading-font uppercase">
                        {{ substr($pegawai->nama, 0, 2) }}
                    </div>
                </div>
            </div>
            @if (Auth::user()->isAdminTU())
                <div class="absolute top-4 right-4">
                    <a href="{{ route('pegawai.edit', $pegawai->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs backdrop-blur-md transition-all border border-white/10">
                        ✏️ Edit Profil
                    </a>
                </div>
            @endif
        </div>

        <div class="pt-16 p-8">
            <div class="mb-6 pb-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-slate-800 heading-font">{{ $pegawai->nama }}</h3>
                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 font-bold rounded-xl text-[10px] mt-2 border border-blue-100 uppercase tracking-wider">
                    {{ $pegawai->jabatan }}
                </span>
            </div>

            <!-- Profile Info Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 text-xs">
                <div>
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1.5">Nomor Induk Pegawai (NIP)</span>
                    <span class="text-slate-800 font-bold text-sm">{{ $pegawai->nip ?: '-' }}</span>
                </div>

                <div>
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1.5">Jenis Kelamin</span>
                    <span class="text-slate-800 font-semibold text-sm">{{ $pegawai->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>

                <div>
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1.5">Nomor Handphone / WA</span>
                    <span class="text-slate-800 font-semibold text-sm">{{ $pegawai->no_hp ?: '-' }}</span>
                </div>

                <div>
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1.5">Status Keaktifan</span>
                    @if ($pegawai->status === 'aktif')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider mt-1">Aktif</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100 uppercase tracking-wider mt-1">Non-Aktif</span>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1.5">Alamat Domisili</span>
                    <span class="text-slate-700 leading-relaxed block bg-slate-50 border border-slate-100 rounded-2xl p-4 italic">
                        {{ $pegawai->alamat ?: 'Tidak ada informasi alamat.' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
