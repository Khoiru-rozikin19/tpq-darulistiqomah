<?php

use App\Models\User;
use App\Models\Pegawai;

test('guest cannot access pegawai index', function () {
    $response = $this->get(route('pegawai.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can access pegawai index', function () {
    $user = User::factory()->create(['role' => 'kepala_madrasah']);
    $response = $this->actingAs($user)->get(route('pegawai.index'));
    $response->assertStatus(200);
});

test('kepala madrasah cannot access create page', function () {
    $user = User::factory()->create(['role' => 'kepala_madrasah']);
    $response = $this->actingAs($user)->get(route('pegawai.create'));
    $response->assertStatus(403);
});

test('admin tu can access create page', function () {
    $user = User::factory()->create(['role' => 'admin_tu']);
    $response = $this->actingAs($user)->get(route('pegawai.create'));
    $response->assertStatus(200);
});

test('admin tu can store new pegawai', function () {
    $user = User::factory()->create(['role' => 'admin_tu']);
    $response = $this->actingAs($user)->post(route('pegawai.store'), [
        'nip' => '1234567890',
        'nama' => 'Ustadz Baru',
        'jenis_kelamin' => 'L',
        'jabatan' => 'Guru',
        'no_hp' => '081299999999',
        'alamat' => 'Alamat baru',
        'status' => 'aktif',
    ]);

    $response->assertRedirect(route('pegawai.index'));
    $this->assertDatabaseHas('pegawais', [
        'nama' => 'Ustadz Baru',
        'jabatan' => 'Guru',
    ]);
});

test('admin tu can update pegawai', function () {
    $user = User::factory()->create(['role' => 'admin_tu']);
    $pegawai = Pegawai::create([
        'nip' => '987654321',
        'nama' => 'Pegawai Lama',
        'jenis_kelamin' => 'P',
        'jabatan' => 'Guru',
        'no_hp' => '081288888888',
        'alamat' => 'Alamat lama',
        'status' => 'aktif',
    ]);

    $response = $this->actingAs($user)->put(route('pegawai.update', $pegawai->id), [
        'nip' => '987654321',
        'nama' => 'Nama Diubah',
        'jenis_kelamin' => 'P',
        'jabatan' => 'Admin TU',
        'no_hp' => '081288888888',
        'alamat' => 'Alamat lama',
        'status' => 'non_aktif',
    ]);

    $response->assertRedirect(route('pegawai.index'));
    $this->assertDatabaseHas('pegawais', [
        'id' => $pegawai->id,
        'nama' => 'Nama Diubah',
        'status' => 'non_aktif',
    ]);
});

test('admin tu can delete pegawai', function () {
    $user = User::factory()->create(['role' => 'admin_tu']);
    $pegawai = Pegawai::create([
        'nip' => '11223344',
        'nama' => 'Pegawai Dihapus',
        'jenis_kelamin' => 'L',
        'jabatan' => 'Guru',
        'status' => 'aktif',
    ]);

    $response = $this->actingAs($user)->delete(route('pegawai.destroy', $pegawai->id));

    $response->assertRedirect(route('pegawai.index'));
    $this->assertDatabaseMissing('pegawais', [
        'id' => $pegawai->id,
    ]);
});
