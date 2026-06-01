@extends('layouts.app')

@section('title', 'Tambah Pegawai')
@section('page_title', 'Form Registrasi Pegawai')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back link -->
    <a href="{{ route('pegawai.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-blue-600 mb-6 transition-colors">
        &larr; Kembali ke Daftar Pegawai
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-blue-700 to-blue-800 text-white">
            <h3 class="text-lg font-bold heading-font">Registrasi Pegawai Baru</h3>
            <p class="text-xs text-blue-100 mt-1">Silakan lengkapi formulir di bawah ini dengan data pegawai yang valid.</p>
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

        <form action="{{ route('pegawai.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf

            <!-- 1. Data Kepegawaian -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 border-b border-slate-100 pb-2">Informasi Kepegawaian</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="nip" class="block text-xs font-semibold text-slate-500 mb-2">Nomor Induk Pegawai (NIP)</label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip') }}" placeholder="Contoh: 19851025..." 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                    </div>
                    <div>
                        <label for="jabatan" class="block text-xs font-semibold text-slate-500 mb-2">Jabatan / Peran</label>
                        <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan') }}" required placeholder="Contoh: Kepala Madrasah, Admin TU, Guru" 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50" list="jabatan-suggestions">
                        <datalist id="jabatan-suggestions">
                            <option value="Kepala Madrasah">
                            <option value="Admin TU">
                            <option value="Guru">
                            <option value="Kepala Yayasan">
                            <option value="Bendahara">
                        </datalist>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-500 mb-2">Status Pegawai</label>
                        <select name="status" id="status" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50 font-semibold">
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="non_aktif" {{ old('status') === 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Data Pribadi & Kontak -->
            <div class="pt-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 border-b border-slate-100 pb-2">Informasi Diri & Kontak</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nama" class="block text-xs font-semibold text-slate-500 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap beserta gelar" 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-xs font-semibold text-slate-500 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" required class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-slate-500 mb-2">Nomor HP (WhatsApp)</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 0812xxxxxxxx" 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-xs font-semibold text-slate-500 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3" placeholder="Masukkan alamat lengkap domisili pegawai saat ini" 
                            class="w-full px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs text-slate-700 bg-slate-50/50">{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('pegawai.index') }}" class="px-5 py-3 border border-slate-200 text-slate-500 hover:text-slate-800 font-semibold rounded-2xl text-xs transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl text-xs transition-all hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] heading-font">
                    Simpan Data Pegawai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
