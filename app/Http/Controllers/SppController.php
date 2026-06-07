<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\SppPayment;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SppController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $bulan = $request->input('bulan');
        $tahunAjaran = $request->input('tahun_ajaran');

        $query = SppPayment::with('santri', 'user');

        if ($search) {
            $query->whereHas('santri', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($tahunAjaran) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        $payments = $query->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tahunAjarans = SppPayment::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran')->all();
        if (empty($tahunAjarans)) {
            $tahunAjarans = ['2025/2026'];
        }

        return view('spp.index', compact('payments', 'tahunAjarans', 'search', 'bulan', 'tahunAjaran'));
    }

    public function create(Request $request)
    {
        $santriId = $request->input('santri_id');
        $bulanSelected = $request->input('bulan', date('n'));
        
        $currentYear = date('Y');
        $currentMonth = date('n');
        $tahunAjaranDefault = $currentMonth >= 7 
            ? $currentYear . '/' . ($currentYear + 1) 
            : ($currentYear - 1) . '/' . $currentYear;
        
        $tahunAjaranSelected = $request->input('tahun_ajaran', $tahunAjaranDefault);

        $santris = Santri::where('status', 'aktif')->orderBy('nama', 'asc')->get();

        return view('spp.create', compact('santris', 'santriId', 'bulanSelected', 'tahunAjaranSelected'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun_ajaran' => 'required|string|max:20',
            'nominal' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'metode_bayar' => 'required|in:tunai,transfer',
            'keterangan' => 'nullable|string',
        ]);

        // Check if student already paid this month & school year
        $existing = SppPayment::where('santri_id', $validated['santri_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun_ajaran', $validated['tahun_ajaran'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'bulan' => 'Santri ini sudah melakukan pembayaran SPP untuk bulan dan tahun ajaran tersebut.',
            ])->withInput();
        }

        $validated['user_id'] = Auth::id();

        // Create SPP Payment
        $payment = SppPayment::create($validated);

        // Sync with KAS
        $santri = Santri::find($payment->santri_id);
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ][$payment->bulan];

        Kas::create([
            'tanggal' => $payment->tanggal_bayar,
            'jenis' => 'masuk',
            'kategori' => 'SPP',
            'keterangan' => "Penerimaan SPP {$namaBulan} a.n {$santri->nama} (NIS: {$santri->nis})",
            'nominal' => $payment->nominal,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('spp.index')
            ->with('success', 'Pembayaran SPP berhasil dicatat dan masuk ke kas.');
    }

    public function billing(Request $request)
    {
        $search = $request->input('search');
        $bulan = $request->input('bulan', date('n'));
        
        $currentYear = date('Y');
        $currentMonth = date('n');
        $tahunAjaranDefault = $currentMonth >= 7 
            ? $currentYear . '/' . ($currentYear + 1) 
            : ($currentYear - 1) . '/' . $currentYear;
        
        $tahunAjaran = $request->input('tahun_ajaran', $tahunAjaranDefault);

        $query = Santri::where('status', 'aktif');

        // Filter out those who have paid
        $query->whereDoesntHave('sppPayments', function ($q) use ($bulan, $tahunAjaran) {
            $q->where('bulan', $bulan)
              ->where('tahun_ajaran', $tahunAjaran);
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        $santris = $query->orderBy('nama', 'asc')->get();

        $tahunAjarans = SppPayment::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran')->all();
        if (empty($tahunAjarans)) {
            $tahunAjarans = [$tahunAjaranDefault];
        } elseif (!in_array($tahunAjaranDefault, $tahunAjarans)) {
            $tahunAjarans[] = $tahunAjaranDefault;
        }

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('spp.billing', compact('santris', 'tahunAjarans', 'search', 'bulan', 'tahunAjaran', 'namaBulanList'));
    }

    public function show($id)
    {
        $payment = SppPayment::with('santri', 'user')->findOrFail($id);
        $pegawais = \App\Models\Pegawai::where('status', 'aktif')->orderBy('nama', 'asc')->get();
        return view('spp.show', compact('payment', 'pegawais'));
    }

    public function destroy($id)
    {
        $payment = SppPayment::findOrFail($id);

        // Delete payment (deleting event on model deletes corresponding KAS record)
        $payment->delete();

        return redirect()->route('spp.index')
            ->with('success', 'Data pembayaran SPP dan kas masuk terkait berhasil dihapus.');
    }
}
