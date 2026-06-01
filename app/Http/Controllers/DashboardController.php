<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\SppPayment;
use App\Models\Kas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $tahunAjaran = $currentMonth >= 7 
            ? $currentYear . '/' . ($currentYear + 1) 
            : ($currentYear - 1) . '/' . $currentYear;

        // 1. Stats Cards
        $totalSantriAktif = Santri::where('status', 'aktif')->count();
        
        $totalSppBulanIni = SppPayment::where('bulan', $currentMonth)
            ->where('tahun_ajaran', $tahunAjaran)
            ->sum('nominal');

        $kasMasuk = Kas::where('jenis', 'masuk')->sum('nominal');
        $kasKeluar = Kas::where('jenis', 'keluar')->sum('nominal');
        $saldoKas = $kasMasuk - $kasKeluar;

        $sudahBayarSppCount = SppPayment::where('bulan', $currentMonth)
            ->where('tahun_ajaran', $tahunAjaran)
            ->count();
        $belumLunasCount = max(0, $totalSantriAktif - $sudahBayarSppCount);

        // 2. Data for Chart (SPP Payments over past 6 months)
        // We will generate the last 6 months starting from current month
        $chartLabels = [];
        $chartData = [];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $m = $date->month;
            $y = $date->year;
            $ta = $m >= 7 ? $y . '/' . ($y + 1) : ($y - 1) . '/' . $y;
            
            $sum = SppPayment::where('bulan', $m)
                ->where('tahun_ajaran', $ta)
                ->sum('nominal');

            $chartLabels[] = $namaBulan[$m] . ' ' . $y;
            $chartData[] = (float) $sum;
        }

        // 3. Recent Transactions
        $recentSpp = SppPayment::with('santri')
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $recentKas = Kas::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSantriAktif',
            'totalSppBulanIni',
            'saldoKas',
            'belumLunasCount',
            'chartLabels',
            'chartData',
            'recentSpp',
            'recentKas',
            'tahunAjaran'
        ));
    }
}
