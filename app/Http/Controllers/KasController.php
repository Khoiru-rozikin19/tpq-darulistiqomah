<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->input('jenis');
        $kategori = $request->input('kategori');
        $search = $request->input('search');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $isPrint = $request->input('print') === 'true';

        $query = Kas::with('user');

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where('keterangan', 'like', "%{$search}%");
        }

        if ($tanggalMulai) {
            $query->whereDate('tanggal', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai) {
            $query->whereDate('tanggal', '<=', $tanggalSelesai);
        }

        // Calculate filtered statistics
        $totalMasuk = (float) (clone $query)->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) (clone $query)->where('jenis', 'keluar')->sum('nominal');
        $saldoPeriode = $totalMasuk - $totalKeluar;

        // Overall stats (for quick headers)
        $overallMasuk = (float) Kas::where('jenis', 'masuk')->sum('nominal');
        $overallKeluar = (float) Kas::where('jenis', 'keluar')->sum('nominal');
        $overallSaldo = $overallMasuk - $overallKeluar;

        // Distinct categories for filters
        $kategories = Kas::select('kategori')->distinct()->pluck('kategori')->all();
        if (empty($kategories)) {
            $kategories = ['SPP', 'Infaq', 'Dana Hibah', 'Listrik & Air', 'ATK', 'Bisyarah Guru', 'Pemeliharaan'];
        }

        if ($isPrint) {
            $kasEntries = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();
            $pegawais = \App\Models\Pegawai::where('status', 'aktif')->orderBy('nama', 'asc')->get();
            return view('kas.report', compact(
                'kasEntries',
                'totalMasuk',
                'totalKeluar',
                'saldoPeriode',
                'tanggalMulai',
                'tanggalSelesai',
                'jenis',
                'kategori',
                'pegawais'
            ));
        }

        $kasEntries = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('kas.index', compact(
            'kasEntries',
            'kategories',
            'jenis',
            'kategori',
            'search',
            'tanggalMulai',
            'tanggalSelesai',
            'totalMasuk',
            'totalKeluar',
            'saldoPeriode',
            'overallSaldo'
        ));
    }

    public function create()
    {
        return view('kas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'nominal' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();

        Kas::create($validated);

        return redirect()->route('kas.index')
            ->with('success', 'Transaksi KAS berhasil dicatat.');
    }

    public function edit($id)
    {
        $kas = Kas::findOrFail($id);
        
        // Prevent editing SPP payments directly from KAS to maintain integrity
        if ($kas->kategori === 'SPP') {
            return redirect()->route('kas.index')
                ->with('error', 'Transaksi KAS bersumber dari SPP tidak dapat diedit secara langsung. Silakan edit via menu Pembayaran SPP jika diperlukan.');
        }

        return view('kas.edit', compact('kas'));
    }

    public function update(Request $request, $id)
    {
        $kas = Kas::findOrFail($id);

        if ($kas->kategori === 'SPP') {
            return redirect()->route('kas.index')
                ->with('error', 'Transaksi KAS bersumber dari SPP tidak dapat diubah.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'nominal' => 'required|numeric|min:0',
        ]);

        $kas->update($validated);

        return redirect()->route('kas.index')
            ->with('success', 'Transaksi KAS berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kas = Kas::findOrFail($id);

        if ($kas->kategori === 'SPP') {
            return redirect()->route('kas.index')
                ->with('error', 'Transaksi KAS bersumber dari SPP tidak dapat dihapus langsung dari sini. Silakan hapus via menu Pembayaran SPP.');
        }

        $kas->delete();

        return redirect()->route('kas.index')
            ->with('success', 'Transaksi KAS berhasil dihapus.');
    }
}
