<?php

use App\Models\User;
use App\Models\Santri;
use App\Models\SppPayment;
use App\Models\Kas;

test('spp payment creation and deletion logic', function () {
    $user = User::factory()->create(['role' => 'admin_tu']);
    
    $santri = Santri::create([
        'nis' => '12345',
        'nama' => 'Ahmad Santri',
        'jenis_kelamin' => 'L',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '2015-05-05',
        'alamat' => 'Jl. Merdeka No. 1',
        'nama_wali' => 'Wali Ahmad',
        'no_hp_wali' => '081234567890',
        'kelas' => '1A',
        'tahun_masuk' => 2025,
        'status' => 'aktif',
    ]);

    // Store SPP payment
    $response = $this->actingAs($user)->post(route('spp.store'), [
        'santri_id' => $santri->id,
        'bulan' => 6,
        'tahun_ajaran' => '2025/2026',
        'nominal' => 20000,
        'tanggal_bayar' => '2026-06-07',
        'metode_bayar' => 'tunai',
        'keterangan' => 'Pembayaran lunas',
    ]);

    $response->assertRedirect(route('spp.index'));
    
    // Assert SppPayment exists
    $this->assertDatabaseHas('spp_payments', [
        'santri_id' => $santri->id,
        'bulan' => 6,
        'tahun_ajaran' => '2025/2026',
    ]);

    // Assert Kas exists
    $this->assertDatabaseHas('kas', [
        'kategori' => 'SPP',
        'keterangan' => 'Penerimaan SPP Juni a.n Ahmad Santri (NIS: 12345)',
        'nominal' => 20000.00,
    ]);

    $payment = SppPayment::first();

    // Now call destroy
    $responseDelete = $this->actingAs($user)->delete(route('spp.destroy', $payment->id));
    $responseDelete->assertRedirect(route('spp.index'));

    // Assert SppPayment is deleted
    $this->assertDatabaseMissing('spp_payments', [
        'id' => $payment->id,
    ]);

    // Assert Kas is deleted
    $this->assertDatabaseMissing('kas', [
        'kategori' => 'SPP',
        'keterangan' => 'Penerimaan SPP Juni a.n Ahmad Santri (NIS: 12345)',
    ]);
});

test('deleting a santri deletes their spp payments and corresponding kas records', function () {
    $user = User::factory()->create(['role' => 'admin_tu']);
    
    $santri = Santri::create([
        'nis' => '67890',
        'nama' => 'Budi Santri',
        'jenis_kelamin' => 'L',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '2016-06-06',
        'alamat' => 'Jl. Merdeka No. 2',
        'nama_wali' => 'Wali Budi',
        'no_hp_wali' => '081234567891',
        'kelas' => '1B',
        'tahun_masuk' => 2025,
        'status' => 'aktif',
    ]);

    // Store SPP payment
    $this->actingAs($user)->post(route('spp.store'), [
        'santri_id' => $santri->id,
        'bulan' => 7,
        'tahun_ajaran' => '2025/2026',
        'nominal' => 20000,
        'tanggal_bayar' => '2026-06-07',
        'metode_bayar' => 'tunai',
        'keterangan' => 'Pembayaran lunas',
    ]);

    // Assert SppPayment exists
    $this->assertDatabaseHas('spp_payments', [
        'santri_id' => $santri->id,
        'bulan' => 7,
    ]);

    // Assert Kas exists
    $this->assertDatabaseHas('kas', [
        'kategori' => 'SPP',
        'keterangan' => 'Penerimaan SPP Juli a.n Budi Santri (NIS: 67890)',
    ]);

    // Now delete santri
    $responseDelete = $this->actingAs($user)->delete(route('santri.destroy', $santri->id));
    $responseDelete->assertRedirect(route('santri.index'));

    // Assert Santri is deleted
    $this->assertDatabaseMissing('santris', [
        'id' => $santri->id,
    ]);

    // Assert SppPayment is deleted
    $this->assertDatabaseMissing('spp_payments', [
        'santri_id' => $santri->id,
    ]);

    // Assert Kas is deleted
    $this->assertDatabaseMissing('kas', [
        'kategori' => 'SPP',
        'keterangan' => 'Penerimaan SPP Juli a.n Budi Santri (NIS: 67890)',
    ]);
});

test('deleting spp payment after student name change still deletes corresponding kas record', function () {
    $user = User::factory()->create(['role' => 'admin_tu']);
    
    $santri = Santri::create([
        'nis' => '11111',
        'nama' => 'Original Name',
        'jenis_kelamin' => 'L',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '2015-05-05',
        'alamat' => 'Jl. Merdeka No. 1',
        'nama_wali' => 'Wali Ahmad',
        'no_hp_wali' => '081234567890',
        'kelas' => '1A',
        'tahun_masuk' => 2025,
        'status' => 'aktif',
    ]);

    // Store SPP payment
    $this->actingAs($user)->post(route('spp.store'), [
        'santri_id' => $santri->id,
        'bulan' => 6,
        'tahun_ajaran' => '2025/2026',
        'nominal' => 20000,
        'tanggal_bayar' => '2026-06-07',
        'metode_bayar' => 'tunai',
        'keterangan' => 'Pembayaran lunas',
    ]);

    // Update santri name
    $santri->update(['nama' => 'Changed Name']);

    // Now delete SPP payment
    $payment = SppPayment::where('santri_id', $santri->id)->first();
    $responseDelete = $this->actingAs($user)->delete(route('spp.destroy', $payment->id));
    $responseDelete->assertRedirect(route('spp.index'));

    // Assert Kas is deleted
    $this->assertDatabaseMissing('kas', [
        'kategori' => 'SPP',
        'keterangan' => 'Penerimaan SPP Juni a.n Original Name (NIS: 11111)',
    ]);
});
