<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('kas:clean-orphans', function () {
    $this->info('Memulai pembersihan transaksi Kas SPP yang yatim/orphan...');

    $namaBulanMap = [
        'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
        'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
        'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
    ];

    $sppKasEntries = \App\Models\Kas::where('kategori', 'SPP')->get();
    $deletedCount = 0;

    foreach ($sppKasEntries as $kas) {
        if (preg_match('/Penerimaan SPP (\w+) a\.n ([^\(]+?)(?:\s*\(NIS:\s*(\d+)\))?$/', $kas->keterangan, $matches)) {
            $bulanName = ucfirst(strtolower($matches[1]));
            $studentName = trim($matches[2]);
            $nis = isset($matches[3]) ? trim($matches[3]) : null;

            $bulanNum = $namaBulanMap[$bulanName] ?? null;

            if (!$bulanNum) {
                $this->warn("Format bulan tidak dikenal untuk Kas ID: {$kas->id} ('{$bulanName}')");
                continue;
            }

            // Cari santri berdasarkan NIS atau nama
            $santri = null;
            if ($nis) {
                $santri = \App\Models\Santri::where('nis', $nis)->first();
            } else {
                $santri = \App\Models\Santri::where('nama', $studentName)->first();
            }

            $isOrphan = false;
            if (!$santri) {
                $isOrphan = true;
                $reason = "Santri '{$studentName}'" . ($nis ? " (NIS: {$nis})" : "") . " tidak ditemukan.";
            } else {
                // Cari spp payment
                $hasPayment = \App\Models\SppPayment::where('santri_id', $santri->id)
                    ->where('bulan', $bulanNum)
                    ->exists();

                if (!$hasPayment) {
                    $isOrphan = true;
                    $reason = "Pembayaran SPP bulan {$bulanName} untuk santri '{$santri->nama}' tidak ditemukan.";
                }
            }

            if ($isOrphan) {
                $this->line("Menghapus Kas ID: {$kas->id} | Keterangan: '{$kas->keterangan}' | Alasan: {$reason}");
                $kas->delete();
                $deletedCount++;
            }
        } else {
            $this->warn("Kas ID: {$kas->id} tidak memiliki format keterangan SPP standar: '{$kas->keterangan}'");
        }
    }

    $this->info("Pembersihan selesai. Total {$deletedCount} transaksi Kas SPP berhasil dihapus.");
})->purpose('Clean up orphan SPP Kas entries that do not have corresponding SppPayment records');
