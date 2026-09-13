<?php

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Services\DynamicQrService;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dynamic qr token is valid for current window and rejected when expired', function () {
    $service = new DynamicQrService;

    $token = $service->generateToken();
    expect($service->validateToken($token))->toBeTrue();

    // Expired token (from 10 windows ago)
    $expiredToken = $service->generateToken($service->getCurrentWindow() - 10);
    expect($service->validateToken($expiredToken))->toBeFalse();

    // Tampered token
    expect($service->validateToken('KAISAR:12345:fakehash'))->toBeFalse();
    expect($service->validateToken('INVALID'))->toBeFalse();
});

test('employee attendance can be recorded using dynamic qr token', function () {
    $this->seed(DatabaseSeeder::class);

    $karyawan = Karyawan::first();
    // Clear attendance records for clean test
    Presensi::query()->delete();

    $service = new DynamicQrService;
    $token = $service->generateToken();

    // First scan -> Clock in
    $checkIn = $service->recordAttendance($karyawan, $token);
    expect($checkIn['success'])->toBeTrue()
        ->and($checkIn['type'])->toBe('check_in');

    $presensi = Presensi::where('karyawan_id', $karyawan->id)->whereDate('tanggal', Carbon::today())->first();
    expect($presensi)->not->toBeNull()
        ->and($presensi->status)->toBe('hadir')
        ->and($presensi->jam_masuk)->not->toBeNull()
        ->and($presensi->jam_keluar)->toBeNull();

    // Second scan -> Clock out
    $checkOut = $service->recordAttendance($karyawan, $token);
    expect($checkOut['success'])->toBeTrue()
        ->and($checkOut['type'])->toBe('check_out');

    $presensi->refresh();
    expect($presensi->jam_keluar)->not->toBeNull();

    // Third scan -> Already completed
    $thirdScan = $service->recordAttendance($karyawan, $token);
    expect($thirdScan['success'])->toBeFalse()
        ->and($thirdScan['type'])->toBe('already_completed');
});
