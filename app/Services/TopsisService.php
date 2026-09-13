<?php

namespace App\Services;

use App\Models\HasilTopsis;
use App\Models\Karyawan;
use App\Models\Kriteria;
use App\Models\Penilaian;

class TopsisService
{
    /**
     * Run TOPSIS calculation for a given period or custom dataset.
     *
     * @return array{
     *     kriterias: array<int, array{id: int, kode: string, nama: string, bobot: float, jenis: string}>,
     *     karyawans: array<int, array{id: int, kode: string, nama: string, jabatan: string}>,
     *     matrix_x: array<int, array<string, float>>,
     *     divisors: array<string, float>,
     *     matrix_r: array<int, array<string, float>>,
     *     matrix_y: array<int, array<string, float>>,
     *     ideal_positive: array<string, float>,
     *     ideal_negative: array<string, float>,
     *     distances: array<int, array{d_plus: float, d_minus: float}>,
     *     rankings: array<int, array{
     *         karyawan_id: int,
     *         kode: string,
     *         nama: string,
     *         jabatan: string,
     *         d_plus: float,
     *         d_minus: float,
     *         preference: float,
     *         rank: int,
     *         keterangan: string
     *     }>
     * }
     */
    public function calculate(string $periode = '2026-09'): array
    {
        $kriterias = Kriteria::orderBy('kode')->get();
        $karyawans = Karyawan::orderBy('kode_karyawan')->get();

        $kriteriaList = [];
        foreach ($kriterias as $k) {
            $kriteriaList[$k->kode] = [
                'id' => $k->id,
                'kode' => $k->kode,
                'nama' => $k->nama_kriteria,
                'bobot' => (float) $k->bobot,
                'jenis' => strtolower($k->jenis),
            ];
        }

        $karyawanList = [];
        foreach ($karyawans as $k) {
            $karyawanList[$k->id] = [
                'id' => $k->id,
                'kode' => $k->kode_karyawan,
                'nama' => $k->nama,
                'jabatan' => $k->jabatan,
            ];
        }

        // Fetch scores
        $penilaians = Penilaian::where('periode', $periode)->get();
        $matrixX = [];

        foreach ($karyawanList as $kId => $kInfo) {
            foreach ($kriteriaList as $kode => $crit) {
                $score = $penilaians->first(fn ($p) => $p->karyawan_id == $kId && $p->kriteria_id == $crit['id']);
                $matrixX[$kId][$kode] = $score ? (float) $score->nilai : 0.0;
            }
        }

        return $this->compute(array_values($kriteriaList), array_values($karyawanList), $matrixX);
    }

    /**
     * Pure calculation engine for TOPSIS.
     *
     * @param  array<int, array{id?: int, kode: string, nama?: string, bobot: float, jenis: string}>  $kriterias
     * @param  array<int, array{id: int, kode: string, nama: string, jabatan?: string}>  $karyawans
     * @param  array<int, array<string, float>>  $matrixX  Keyed by karyawan id
     */
    public function compute(array $kriterias, array $karyawans, array $matrixX): array
    {
        if (empty($karyawans) || empty($kriterias)) {
            return [
                'kriterias' => $kriterias,
                'karyawans' => $karyawans,
                'matrix_x' => [],
                'divisors' => [],
                'matrix_r' => [],
                'matrix_y' => [],
                'ideal_positive' => [],
                'ideal_negative' => [],
                'distances' => [],
                'rankings' => [],
            ];
        }

        // Step 1 & 2: Calculate Divisors: sqrt(sum(x_ij^2))
        $divisors = [];
        foreach ($kriterias as $crit) {
            $kode = $crit['kode'];
            $sumSq = 0.0;
            foreach ($karyawans as $k) {
                $val = $matrixX[$k['id']][$kode] ?? 0.0;
                $sumSq += ($val * $val);
            }
            $divisors[$kode] = $sumSq > 0 ? sqrt($sumSq) : 1.0;
        }

        // Normalized Matrix R: r_ij = x_ij / divisor_j
        $matrixR = [];
        foreach ($karyawans as $k) {
            $kId = $k['id'];
            foreach ($kriterias as $crit) {
                $kode = $crit['kode'];
                $val = $matrixX[$kId][$kode] ?? 0.0;
                $matrixR[$kId][$kode] = $divisors[$kode] > 0 ? ($val / $divisors[$kode]) : 0.0;
            }
        }

        // Step 3: Weighted Normalized Matrix Y: y_ij = w_j * r_ij
        $matrixY = [];
        foreach ($karyawans as $k) {
            $kId = $k['id'];
            foreach ($kriterias as $crit) {
                $kode = $crit['kode'];
                $matrixY[$kId][$kode] = (float) $crit['bobot'] * $matrixR[$kId][$kode];
            }
        }

        // Step 4: Ideal Positive (A+) and Ideal Negative (A-)
        $idealPos = [];
        $idealNeg = [];
        foreach ($kriterias as $crit) {
            $kode = $crit['kode'];
            $colValues = [];
            foreach ($karyawans as $k) {
                $colValues[] = $matrixY[$k['id']][$kode];
            }

            $maxVal = max($colValues);
            $minVal = min($colValues);

            if ($crit['jenis'] === 'cost') {
                $idealPos[$kode] = $minVal;
                $idealNeg[$kode] = $maxVal;
            } else {
                // benefit
                $idealPos[$kode] = $maxVal;
                $idealNeg[$kode] = $minVal;
            }
        }

        // Step 5: Distances D+ and D-
        $distances = [];
        foreach ($karyawans as $k) {
            $kId = $k['id'];
            $sumDPlus = 0.0;
            $sumDMinus = 0.0;

            foreach ($kriterias as $crit) {
                $kode = $crit['kode'];
                $y = $matrixY[$kId][$kode];
                $sumDPlus += pow($y - $idealPos[$kode], 2);
                $sumDMinus += pow($y - $idealNeg[$kode], 2);
            }

            $distances[$kId] = [
                'd_plus' => sqrt($sumDPlus),
                'd_minus' => sqrt($sumDMinus),
            ];
        }

        // Step 6: Preference value V_i = D_i^- / (D_i^- + D_i^+)
        $rankings = [];
        foreach ($karyawans as $k) {
            $kId = $k['id'];
            $dPlus = $distances[$kId]['d_plus'];
            $dMinus = $distances[$kId]['d_minus'];
            $totalD = $dMinus + $dPlus;
            $pref = $totalD > 0 ? ($dMinus / $totalD) : 0.0;

            $keterangan = $this->determineKeterangan($pref);

            $rankings[] = [
                'karyawan_id' => $kId,
                'kode' => $k['kode'],
                'nama' => $k['nama'],
                'jabatan' => $k['jabatan'] ?? '',
                'd_plus' => $dPlus,
                'd_minus' => $dMinus,
                'preference' => $pref,
                'rank' => 0,
                'keterangan' => $keterangan,
            ];
        }

        // Step 7: Sort by preference descending
        usort($rankings, fn ($a, $b) => $b['preference'] <=> $a['preference']);

        // Assign ranking number and crown top performer
        foreach ($rankings as $idx => &$item) {
            $item['rank'] = $idx + 1;
            if ($idx === 0 && $item['preference'] >= 0.50) {
                $item['keterangan'] = 'Kinerja Terbaik';
            }
        }
        unset($item);

        return [
            'kriterias' => $kriterias,
            'karyawans' => $karyawans,
            'matrix_x' => $matrixX,
            'divisors' => $divisors,
            'matrix_r' => $matrixR,
            'matrix_y' => $matrixY,
            'ideal_positive' => $idealPos,
            'ideal_negative' => $idealNeg,
            'distances' => $distances,
            'rankings' => $rankings,
        ];
    }

    /**
     * Determine performance label based on preference value.
     */
    protected function determineKeterangan(float $v): string
    {
        if ($v >= 0.55) {
            return 'Kinerja Terbaik';
        }
        if ($v >= 0.50) {
            return 'Baik';
        }
        if ($v >= 0.40) {
            return 'Cukup';
        }

        return 'Kurang';
    }

    /**
     * Persist calculated results into database table hasil_topsises.
     */
    public function saveResults(string $periode, array $rankings): void
    {
        foreach ($rankings as $item) {
            HasilTopsis::updateOrCreate(
                [
                    'karyawan_id' => $item['karyawan_id'],
                    'periode' => $periode,
                ],
                [
                    'd_plus' => round($item['d_plus'], 6),
                    'd_minus' => round($item['d_minus'], 6),
                    'nilai_preferensi' => round($item['preference'], 4),
                    'ranking' => $item['rank'],
                    'keterangan' => $item['keterangan'],
                ]
            );
        }
    }
}
