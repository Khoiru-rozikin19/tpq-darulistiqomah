@extends('layouts.app')

@section('title', 'Edit Santri')
@section('page_title', 'Form Edit Santri')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back link -->
    <a href="{{ route('santri.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 mb-6 transition-colors">
        &larr; Kembali ke Daftar Santri
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-blue-700 to-blue-800 text-white flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold heading-font">Edit Data Santri</h3>
                <p class="text-xs text-blue-100 mt-1">Mengedit data untuk santri: <strong>{{ $santri->nama }}</strong></p>
            </div>
            <span class="text-xs bg-white/20 text-white px-3 py-1.5 rounded-xl font-bold heading-font">{{ $santri->nis }}</span>
        </div>

        @if ($errors->any())
            <div class="p-6 bg-red-50 border-b border-red-100 text-red-700 text-xs">
                <p class="font-bold mb-2">Terjadi kesalahan input:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('santri.update', $santri->id) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Data Akademik -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 border-b border-slate-100 pb-2">Informasi Akademik</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="nis" class="block text-xs font-semibold text-slate-500 mb-2">Nomor Induk Santri (NIS)</label>
                        <input type="text" name="nis" id="nis" value="{{ old('nis', $santri->nis) }}" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                    </div>
                    <div>
                        <label for="kelas" class="block text-xs font-semibold text-slate-500 mb-2">Kelas / Kelompok</label>
                        <input type="text" name="kelas" id="kelas" value="{{ old('kelas', $santri->kelas) }}" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>
                    <div>
                        <label for="tahun_masuk" class="block text-xs font-semibold text-slate-500 mb-2">Tahun Masuk</label>
                        <input type="number" name="tahun_masuk" id="tahun_masuk" value="{{ old('tahun_masuk', $santri->tahun_masuk) }}" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>
                </div>
            </div>

            <!-- 2. Data Pribadi -->
            <div class="pt-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 border-b border-slate-100 pb-2">Informasi Diri Santri</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nama" class="block text-xs font-semibold text-slate-500 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $santri->nama) }}" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-xs font-semibold text-slate-500 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                            <option value="L" {{ old('jenis_kelamin', $santri->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $santri->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-500 mb-2">Status Santri</label>
                        <select name="status" id="status" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                            <option value="aktif" {{ old('status', $santri->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="alumni" {{ old('status', $santri->status) === 'alumni' ? 'selected' : '' }}>Alumni</option>
                            <option value="keluar" {{ old('status', $santri->status) === 'keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>
                    </div>

                    <div>
                        <label for="tempat_lahir" class="block text-xs font-semibold text-slate-500 mb-2">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $santri->tempat_lahir) }}" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-xs font-semibold text-slate-500 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $santri->tanggal_lahir->format('Y-m-d')) }}" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-xs font-semibold text-slate-500 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">{{ old('alamat', $santri->alamat) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. Wali & Kontak -->
            <div class="pt-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 border-b border-slate-100 pb-2">Informasi Wali & Kontak</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_wali" class="block text-xs font-semibold text-slate-500 mb-2">Nama Wali Santri</label>
                        <input type="text" name="nama_wali" id="nama_wali" value="{{ old('nama_wali', $santri->nama_wali) }}" required 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>

                    <div>
                        <label for="no_hp_wali" class="block text-xs font-semibold text-slate-500 mb-2">Nomor HP Wali (WhatsApp)</label>
                        <input type="text" name="no_hp_wali" id="no_hp_wali" value="{{ old('no_hp_wali', $santri->no_hp_wali) }}" 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>
                </div>
            </div>

            <!-- 4. Media -->
            <div class="pt-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 border-b border-slate-100 pb-2">Unggah Foto Profil</h4>
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    @if ($santri->foto)
                        <div class="flex-shrink-0">
                            <span class="block text-xs font-semibold text-slate-400 mb-2">Foto Saat Ini:</span>
                            <img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto {{ $santri->nama }}" class="w-24 h-24 rounded-2xl object-cover border border-slate-100 shadow-sm">
                        </div>
                    @endif
                    <div class="flex-1 w-full">
                        <label for="foto" class="block text-xs font-semibold text-slate-500 mb-2">Ganti Foto Santri</label>
                        <input type="file" name="foto" id="foto" accept="image/*" 
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all border border-slate-200 rounded-xl p-2.5 bg-slate-50/50">
                        <p class="text-[10px] text-slate-400 mt-2">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, JPEG, PNG. Ukuran maks: 2 MB.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('santri.index') }}" class="px-5 py-3 border border-slate-200 text-slate-500 hover:text-slate-800 font-semibold rounded-2xl text-xs transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
