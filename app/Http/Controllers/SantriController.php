<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\SppPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $kelas = $request->input('kelas');

        $query = Santri::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        $santris = $query->orderBy('nama', 'asc')->paginate(15)->withQueryString();
        $kelases = Santri::select('kelas')->distinct()->pluck('kelas')->all();

        return view('santri.index', compact('santris', 'kelases', 'search', 'status', 'kelas'));
    }

    public function create()
    {
        return view('santri.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:santris,nis',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_wali' => 'required|string|max:255',
            'no_hp_wali' => 'nullable|string|max:20',
            'kelas' => 'required|string|max:50',
            'tahun_masuk' => 'required|numeric|digits:4',
            'status' => 'required|in:aktif,alumni,keluar',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('santris', 'public');
            $validated['foto'] = $path;
        }

        Santri::create($validated);

        return redirect()->route('santri.index')
            ->with('success', 'Data santri berhasil ditambahkan.');
    }

    public function show($id)
    {
        $santri = Santri::findOrFail($id);
        
        $currentYear = date('Y');
        $currentMonth = date('n');
        $tahunAjaranAktif = $currentMonth >= 7 
            ? $currentYear . '/' . ($currentYear + 1) 
            : ($currentYear - 1) . '/' . $currentYear;

        // Retrieve payment history for this student
        $payments = SppPayment::where('santri_id', $id)
            ->orderBy('tahun_ajaran', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // Group payments by academic year
        $paymentsByYear = $payments->groupBy('tahun_ajaran');

        return view('santri.show', compact('santri', 'paymentsByYear', 'tahunAjaranAktif'));
    }

    public function edit($id)
    {
        $santri = Santri::findOrFail($id);
        return view('santri.edit', compact('santri'));
    }

    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'required|string|unique:santris,nis,' . $id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_wali' => 'required|string|max:255',
            'no_hp_wali' => 'nullable|string|max:20',
            'kelas' => 'required|string|max:50',
            'tahun_masuk' => 'required|numeric|digits:4',
            'status' => 'required|in:aktif,alumni,keluar',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                Storage::disk('public')->delete($santri->foto);
            }
            
            $path = $request->file('foto')->store('santris', 'public');
            $validated['foto'] = $path;
        }

        $santri->update($validated);

        return redirect()->route('santri.index')
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $santri = Santri::findOrFail($id);
        
        // Delete photo if exists
        if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
            Storage::disk('public')->delete($santri->foto);
        }

        $santri->delete();

        return redirect()->route('santri.index')
            ->with('success', 'Data santri berhasil dihapus.');
    }
}
