<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Santri;
use App\Models\SppPayment;
use App\Models\Kas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Admin TU',
            'email' => 'admin@darulistiqomah.com',
            'password' => Hash::make('password'),
            'role' => 'admin_tu',
        ]);

        $kepala = User::create([
            'name' => 'Kepala Madrasah (Ust. Zainal Ghorib)',
            'email' => 'kepala@darulistiqomah.com',
            'password' => Hash::make('password'),
            'role' => 'kepala_madrasah',
        ]);

        // 2. Seed Santris
        $santrisData = [
            [
                'nis' => '10001',
                'nama' => 'Muhammad Al-Fatih',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2010-05-12',
                'alamat' => 'Jl. Kebagusan Raya No. 12, Pasar Minggu',
                'nama_wali' => 'Budi Santoso',
                'no_hp_wali' => '081234567890',
                'kelas' => '7A',
                'tahun_masuk' => '2023',
                'status' => 'aktif',
            ],
            [
                'nis' => '10002',
                'nama' => 'Aisyah Humaira',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '2011-08-20',
                'alamat' => 'Perumahan Baranangsiang Indah Blok C/4',
                'nama_wali' => 'Hasan Basri',
                'no_hp_wali' => '081398765432',
                'kelas' => '7A',
                'tahun_masuk' => '2023',
                'status' => 'aktif',
            ],
            [
                'nis' => '10003',
                'nama' => 'Yusuf Ibrahim',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Depok',
                'tanggal_lahir' => '2010-12-05',
                'alamat' => 'Jl. Margonda Raya Gg. Kelinci No. 8',
                'nama_wali' => 'Ahmad Ibrahim',
                'no_hp_wali' => '081512345678',
                'kelas' => '8B',
                'tahun_masuk' => '2022',
                'status' => 'aktif',
            ],
            [
                'nis' => '10004',
                'nama' => 'Fatimah Az-Zahra',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Tangerang',
                'tanggal_lahir' => '2009-03-15',
                'alamat' => 'BSD City Sektor 1.2, Blok H3/9',
                'nama_wali' => 'Ridwan Kamil',
                'no_hp_wali' => '081122334455',
                'kelas' => '9C',
                'tahun_masuk' => '2021',
                'status' => 'aktif',
            ],
            [
                'nis' => '10005',
                'nama' => 'Ali bin Abi Thalib',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => '2008-07-22',
                'alamat' => 'Jl. KH. Noer Ali No. 45, Kalimalang',
                'nama_wali' => 'Syarifuddin',
                'no_hp_wali' => '081299887766',
                'kelas' => '9C',
                'tahun_masuk' => '2021',
                'status' => 'alumni',
            ],
        ];

        $santris = [];
        foreach ($santrisData as $data) {
            $santris[] = Santri::create($data);
        }

        // 3. Seed SPP Payments (Tahun Ajaran: 2025/2026, 20000 per bulan)
        // Let's seed for Muhammad Al-Fatih, Aisyah, Yusuf, Fatimah
        // Let's say Al-Fatih has paid Jan, Feb, Mar 2026. Aisyah has paid Jan, Feb. Yusuf paid Jan.
        $monthsToPay = [
            // Al-Fatih
            ['santri_id' => $santris[0]->id, 'bulan' => 1, 'tanggal' => '2026-01-05'],
            ['santri_id' => $santris[0]->id, 'bulan' => 2, 'tanggal' => '2026-02-04'],
            ['santri_id' => $santris[0]->id, 'bulan' => 3, 'tanggal' => '2026-03-05'],
            ['santri_id' => $santris[0]->id, 'bulan' => 4, 'tanggal' => '2026-04-06'],
            ['santri_id' => $santris[0]->id, 'bulan' => 5, 'tanggal' => '2026-05-05'],
            // Aisyah
            ['santri_id' => $santris[1]->id, 'bulan' => 1, 'tanggal' => '2026-01-07'],
            ['santri_id' => $santris[1]->id, 'bulan' => 2, 'tanggal' => '2026-02-06'],
            ['santri_id' => $santris[1]->id, 'bulan' => 3, 'tanggal' => '2026-03-08'],
            // Yusuf
            ['santri_id' => $santris[2]->id, 'bulan' => 1, 'tanggal' => '2026-01-10'],
            ['santri_id' => $santris[2]->id, 'bulan' => 2, 'tanggal' => '2026-02-12'],
            // Fatimah
            ['santri_id' => $santris[3]->id, 'bulan' => 1, 'tanggal' => '2026-01-05'],
        ];

        foreach ($monthsToPay as $pay) {
            SppPayment::create([
                'santri_id' => $pay['santri_id'],
                'bulan' => $pay['bulan'],
                'tahun_ajaran' => '2025/2026',
                'nominal' => 20000,
                'tanggal_bayar' => $pay['tanggal'],
                'metode_bayar' => 'tunai',
                'keterangan' => 'Pembayaran SPP Bulan ' . $pay['bulan'],
                'user_id' => $admin->id,
            ]);

            // Also record in KAS as income
            $santri = Santri::find($pay['santri_id']);
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ][$pay['bulan']];

            Kas::create([
                'tanggal' => $pay['tanggal'],
                'jenis' => 'masuk',
                'kategori' => 'SPP',
                'keterangan' => 'Penerimaan SPP ' . $namaBulan . ' a.n ' . $santri->nama,
                'nominal' => 20000,
                'user_id' => $admin->id,
            ]);
        }

        // 4. Seed other KAS entries (Infaq, Operasional, dll)
        $kasData = [
            [
                'tanggal' => '2026-01-01',
                'jenis' => 'masuk',
                'kategori' => 'Infaq',
                'keterangan' => 'Infaq Hamba Allah untuk pembangunan masjid',
                'nominal' => 5000000.00,
                'user_id' => $kepala->id,
            ],
            [
                'tanggal' => '2026-01-15',
                'jenis' => 'keluar',
                'kategori' => 'Listrik & Air',
                'keterangan' => 'Pembayaran tagihan listrik & air madrasah bulan Januari',
                'nominal' => 450000.00,
                'user_id' => $admin->id,
            ],
            [
                'tanggal' => '2026-02-01',
                'jenis' => 'masuk',
                'kategori' => 'Infaq',
                'keterangan' => 'Sumbangan Al-Qur\'an dan Kitab Kuning dari donatur',
                'nominal' => 1500000.00,
                'user_id' => $kepala->id,
            ],
            [
                'tanggal' => '2026-02-10',
                'jenis' => 'keluar',
                'kategori' => 'ATK',
                'keterangan' => 'Pembelian buku tulis, spidol, dan kertas printer',
                'nominal' => 250000.00,
                'user_id' => $admin->id,
            ],
            [
                'tanggal' => '2026-03-01',
                'jenis' => 'masuk',
                'kategori' => 'Dana Hibah',
                'keterangan' => 'Bantuan operasional dari Kemenag',
                'nominal' => 10000000.00,
                'user_id' => $kepala->id,
            ],
            [
                'tanggal' => '2026-03-12',
                'jenis' => 'keluar',
                'kategori' => 'Bisyarah Guru',
                'keterangan' => 'Bisyarah/Honor asatidzah bulan Maret',
                'nominal' => 6000000.00,
                'user_id' => $admin->id,
            ],
            [
                'tanggal' => '2026-04-10',
                'jenis' => 'keluar',
                'kategori' => 'Pemeliharaan',
                'keterangan' => 'Renovasi kecil genteng bocor kelas 7A',
                'nominal' => 750000.00,
                'user_id' => $admin->id,
            ],
        ];

        foreach ($kasData as $kas) {
            Kas::create($kas);
        }

        // 5. Seed Pegawais
        $pegawais = [
            [
                'nip' => '197508122003121002',
                'nama' => 'K.H. Rahmat',
                'jenis_kelamin' => 'L',
                'jabatan' => 'Kepala Madrasah',
                'no_hp' => '081234567801',
                'alamat' => 'Jl. Kebagusan No. 20, Pasar Minggu',
                'status' => 'aktif',
            ],
            [
                'nip' => '198510252010011005',
                'nama' => 'Ustadz Ahmad',
                'jenis_kelamin' => 'L',
                'jabatan' => 'Admin TU',
                'no_hp' => '081234567802',
                'alamat' => 'Jl. Kebagusan No. 25, Pasar Minggu',
                'status' => 'aktif',
            ],
            [
                'nip' => '199003152015042001',
                'nama' => 'Ustadzah Fatimah',
                'jenis_kelamin' => 'P',
                'jabatan' => 'Guru',
                'no_hp' => '081234567803',
                'alamat' => 'Jl. Kebagusan No. 30, Pasar Minggu',
                'status' => 'aktif',
            ],
            [
                'nip' => '199207222018021003',
                'nama' => 'Ustadz Ali',
                'jenis_kelamin' => 'L',
                'jabatan' => 'Guru',
                'no_hp' => '081234567804',
                'alamat' => 'Jl. Kebagusan No. 35, Pasar Minggu',
                'status' => 'aktif',
            ],
        ];

        foreach ($pegawais as $pegawai) {
            \App\Models\Pegawai::create($pegawai);
        }
    }
}
