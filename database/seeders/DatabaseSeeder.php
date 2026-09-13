<?php

namespace Database\Seeders;

use App\Models\HasilTopsis;
use App\Models\Karyawan;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Presensi;
use App\Models\User;
use App\Services\TopsisService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@kaisarbakery.com'],
            [
                'name' => 'Administrator Kaisar Bakery',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // 2. Create Kriteria (Bab IV Skripsi Hal. 60)
        $kriteriasData = [
            ['kode' => 'C1', 'nama_kriteria' => 'Kehadiran', 'bobot' => 0.25, 'jenis' => 'benefit'],
            ['kode' => 'C2', 'nama_kriteria' => 'Ketepatan Waktu', 'bobot' => 0.20, 'jenis' => 'benefit'],
            ['kode' => 'C3', 'nama_kriteria' => 'Produktivitas', 'bobot' => 0.25, 'jenis' => 'benefit'],
            ['kode' => 'C4', 'nama_kriteria' => 'Kualitas Kerja', 'bobot' => 0.15, 'jenis' => 'benefit'],
            ['kode' => 'C5', 'nama_kriteria' => 'Kerjasama', 'bobot' => 0.15, 'jenis' => 'benefit'],
        ];

        $kriteriaModels = [];
        foreach ($kriteriasData as $item) {
            $kriteriaModels[$item['kode']] = Kriteria::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }

        // 3. Create Karyawan Alternatif A1 - A5 (Bab IV Skripsi)
        $karyawansData = [
            [
                'kode' => 'A1',
                'name' => 'Budi Santoso',
                'username' => 'budi',
                'email' => 'budi@kaisarbakery.com',
                'jabatan' => 'Baker (Produksi Roti)',
                'no_hp' => '081234567891',
            ],
            [
                'kode' => 'A2',
                'name' => 'Siti Aminah',
                'username' => 'siti',
                'email' => 'siti@kaisarbakery.com',
                'jabatan' => 'Kasir / Front Office',
                'no_hp' => '081234567892',
            ],
            [
                'kode' => 'A3',
                'name' => 'Ahmad Hidayat',
                'username' => 'ahmad',
                'email' => 'ahmad@kaisarbakery.com',
                'jabatan' => 'Pastry Chef',
                'no_hp' => '081234567893',
            ],
            [
                'kode' => 'A4',
                'name' => 'Dewi Lestari',
                'username' => 'dewi',
                'email' => 'dewi@kaisarbakery.com',
                'jabatan' => 'Quality Control & Packing',
                'no_hp' => '081234567894',
            ],
            [
                'kode' => 'A5',
                'name' => 'Eko Prasetyo',
                'username' => 'eko',
                'email' => 'eko@kaisarbakery.com',
                'jabatan' => 'Staf Logistik & Bahan Baku',
                'no_hp' => '081234567895',
            ],
        ];

        $karyawanModels = [];
        foreach ($karyawansData as $kData) {
            $user = User::firstOrCreate(
                ['email' => $kData['email']],
                [
                    'name' => $kData['name'],
                    'username' => $kData['username'],
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_KARYAWAN,
                ]
            );

            $karyawan = Karyawan::updateOrCreate(
                ['kode_karyawan' => $kData['kode']],
                [
                    'user_id' => $user->id,
                    'nama' => $kData['name'],
                    'jabatan' => $kData['jabatan'],
                    'no_hp' => $kData['no_hp'],
                ]
            );

            $karyawanModels[$kData['kode']] = $karyawan;
        }

        // 4. Create Penilaian Dataset (Bab IV Matriks Keputusan X Hal. 60-61)
        // A1: 90, 85, 80, 88, 85
        // A2: 80, 90, 85, 82, 90
        // A3: 95, 88, 78, 90, 84
        // A4: 85, 80, 92, 85, 88
        // A5: 75, 82, 88, 80, 86
        $matrixScores = [
            'A1' => ['C1' => 90, 'C2' => 85, 'C3' => 80, 'C4' => 88, 'C5' => 85],
            'A2' => ['C1' => 80, 'C2' => 90, 'C3' => 85, 'C4' => 82, 'C5' => 90],
            'A3' => ['C1' => 95, 'C2' => 88, 'C3' => 78, 'C4' => 90, 'C5' => 84],
            'A4' => ['C1' => 85, 'C2' => 80, 'C3' => 92, 'C4' => 85, 'C5' => 88],
            'A5' => ['C1' => 75, 'C2' => 82, 'C3' => 88, 'C4' => 80, 'C5' => 86],
        ];

        $periode = '2026-09';

        foreach ($matrixScores as $kodeKaryawan => $critScores) {
            $karyawan = $karyawanModels[$kodeKaryawan];
            foreach ($critScores as $kodeKriteria => $score) {
                $kriteria = $kriteriaModels[$kodeKriteria];
                Penilaian::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'kriteria_id' => $kriteria->id,
                        'periode' => $periode,
                    ],
                    [
                        'nilai' => $score,
                    ]
                );
            }
        }

        // 5. Run TOPSIS calculation & populate HasilTopsis
        $topsisService = new TopsisService;
        $result = $topsisService->calculate($periode);
        $topsisService->saveResults($periode, $result['rankings']);

        // 6. Create Presensi sample history
        $today = Carbon::today();
        $counter = 0;
        foreach ($karyawanModels as $karyawan) {
            $counter++;
            $existingPresensi = Presensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $today)
                ->first();

            $dataPresensi = [
                'jam_masuk' => sprintf('07:%02d:00', 30 + ($counter * 5)),
                'jam_keluar' => $counter % 2 === 0 ? '16:05:00' : null,
                'status' => 'hadir',
                'metode' => 'qr_dinamis',
                'token_validasi' => 'KAISAR:SAMPLE:TOKEN',
                'catatan' => 'Presensi QR Dinamis',
            ];

            if ($existingPresensi) {
                $existingPresensi->update($dataPresensi);
            } else {
                Presensi::create(array_merge([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $today->toDateString(),
                ], $dataPresensi));
            }
        }
    }
}
