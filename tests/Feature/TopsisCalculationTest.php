<?php

use App\Models\HasilTopsis;
use App\Services\TopsisService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('topsis calculation correctly computes divisors, normalized matrices, and rankings', function () {
    $this->seed(DatabaseSeeder::class);

    $service = new TopsisService;
    $result = $service->calculate('2026-09');

    // 1. Verify Divisors match Bab IV Step 1 (190.72, 190.24, 189.52, 190.24, 193.70)
    expect(round($result['divisors']['C1'], 2))->toBe(190.72)
        ->and(round($result['divisors']['C2'], 2))->toBe(190.24)
        ->and(round($result['divisors']['C3'], 2))->toBe(189.52)
        ->and(round($result['divisors']['C4'], 2))->toBe(190.24)
        ->and(round($result['divisors']['C5'], 2))->toBe(193.70);

    // 2. Verify Ideal Positive and Negative match Bab IV
    expect(round($result['ideal_positive']['C1'], 4))->toBe(0.1245)
        ->and(round($result['ideal_positive']['C2'], 4))->toBe(0.0946)
        ->and(round($result['ideal_positive']['C3'], 4))->toBe(0.1214)
        ->and(round($result['ideal_positive']['C4'], 4))->toBe(0.0710)
        ->and(round($result['ideal_positive']['C5'], 4))->toBe(0.0697);

    expect(round($result['ideal_negative']['C1'], 4))->toBe(0.0983)
        ->and(round($result['ideal_negative']['C2'], 4))->toBe(0.0841)
        ->and(round($result['ideal_negative']['C3'], 4))->toBe(0.1029)
        ->and(round($result['ideal_negative']['C4'], 4))->toBe(0.0631)
        ->and(round($result['ideal_negative']['C5'], 4))->toBe(0.0650);

    // 3. Verify Rankings count and descending preference
    expect($result['rankings'])->toHaveCount(5);
    for ($i = 0; $i < count($result['rankings']) - 1; $i++) {
        expect($result['rankings'][$i]['preference'])
            ->toBeGreaterThanOrEqual($result['rankings'][$i + 1]['preference']);
    }

    // Top performers have preference > 0.5
    expect($result['rankings'][0]['preference'])->toBeGreaterThan(0.5)
        ->and($result['rankings'][0]['rank'])->toBe(1);
});

test('topsis service can save results to database table hasil_topsises', function () {
    $this->seed(DatabaseSeeder::class);

    $service = new TopsisService;
    $result = $service->calculate('2026-09');
    $service->saveResults('2026-09', $result['rankings']);

    $saved = HasilTopsis::where('periode', '2026-09')->orderBy('ranking')->get();
    expect($saved)->toHaveCount(5);

    $topResult = $saved->first();
    expect($topResult->ranking)->toBe(1)
        ->and((float) $topResult->nilai_preferensi)->toBeGreaterThan(0.5);
});
