<?php

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Services\PresensiExportService;

test('presensi export service queries records by daily, weekly, monthly, month range, and yearly', function () {
    $karyawan = Karyawan::create([
        'kode_karyawan' => 'TEST01',
        'nama' => 'Tester Karyawan',
        'jabatan' => 'Baker',
        'no_hp' => '08123456789',
    ]);

    Presensi::create([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-05-10',
        'jam_masuk' => '08:00',
        'jam_keluar' => '16:00',
        'status' => 'hadir',
        'metode' => 'manual',
    ]);

    Presensi::create([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-05-15',
        'jam_masuk' => '08:05',
        'jam_keluar' => '16:00',
        'status' => 'hadir',
        'metode' => 'qr_dinamis',
    ]);

    Presensi::create([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-06-01',
        'jam_masuk' => '08:00',
        'jam_keluar' => '16:00',
        'status' => 'izin',
        'metode' => 'manual',
    ]);

    $service = new PresensiExportService;

    // Harian
    $harian = $service->queryRecords([
        'tipe_periode' => 'harian',
        'tanggal' => '2026-05-10',
    ]);
    expect($harian)->toHaveCount(1)
        ->and($harian->first()->tanggal->format('Y-m-d'))->toBe('2026-05-10');

    // Mingguan
    $mingguan = $service->queryRecords([
        'tipe_periode' => 'mingguan',
        'tanggal_mulai' => '2026-05-08',
        'tanggal_selesai' => '2026-05-12',
    ]);
    expect($mingguan)->toHaveCount(1);

    // Bulanan
    $bulanan = $service->queryRecords([
        'tipe_periode' => 'bulanan',
        'bulan' => 5,
        'tahun' => 2026,
    ]);
    expect($bulanan)->toHaveCount(2);

    // Rentang Bulan
    $rentang = $service->queryRecords([
        'tipe_periode' => 'rentang_bulan',
        'bulan_mulai' => 5,
        'bulan_selesai' => 6,
        'tahun_rentang' => 2026,
    ]);
    expect($rentang)->toHaveCount(3);

    // Tahunan
    $tahunan = $service->queryRecords([
        'tipe_periode' => 'tahunan',
        'tahun_tunggal' => 2026,
    ]);
    expect($tahunan)->toHaveCount(3);

    // Filter status
    $izinOnly = $service->queryRecords([
        'tipe_periode' => 'tahunan',
        'tahun_tunggal' => 2026,
        'status' => 'izin',
    ]);
    expect($izinOnly)->toHaveCount(1);
});

test('presensi export service generates downloadable xlsx file', function () {
    $karyawan = Karyawan::create([
        'kode_karyawan' => 'TEST02',
        'nama' => 'Budi Tester',
        'jabatan' => 'Pastry',
        'no_hp' => '08129876543',
    ]);

    Presensi::create([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-05-10',
        'jam_masuk' => '07:55',
        'jam_keluar' => '16:00',
        'status' => 'hadir',
        'metode' => 'manual',
    ]);

    $service = new PresensiExportService;
    $data = [
        'tipe_periode' => 'bulanan',
        'bulan' => 5,
        'tahun' => 2026,
    ];

    $records = $service->queryRecords($data);
    $response = $service->downloadExcel($records, $data);

    expect($response->getFile()->getPathname())->toBeFile()
        ->and($response->headers->get('Content-Type'))->toBe('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // Verify XLSX zip signature
    $content = file_get_contents($response->getFile()->getPathname());
    expect(str_starts_with($content, "PK\x03\x04"))->toBeTrue();
});
