<?php

namespace App\Services;

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PresensiExportService
{
    /**
     * @param  array<string, mixed>  $data
     * @return Collection<int, Presensi>
     */
    public function queryRecords(array $data): Collection
    {
        $query = Presensi::query()->with('karyawan');

        $tipe = $data['tipe_periode'] ?? 'bulanan';

        match ($tipe) {
            'harian' => $query->whereDate('tanggal', $data['tanggal'] ?? now()->format('Y-m-d')),
            'mingguan' => $query->whereBetween('tanggal', [
                $data['tanggal_mulai'] ?? now()->startOfWeek()->format('Y-m-d'),
                $data['tanggal_selesai'] ?? now()->endOfWeek()->format('Y-m-d'),
            ]),
            'bulanan' => $query->whereYear('tanggal', (int) ($data['tahun'] ?? now()->format('Y')))
                ->whereMonth('tanggal', (int) ($data['bulan'] ?? now()->format('n'))),
            'rentang_bulan' => (function () use ($query, $data): void {
                $bulanMulai = (int) ($data['bulan_mulai'] ?? 1);
                $bulanSelesai = (int) ($data['bulan_selesai'] ?? now()->format('n'));
                $tahun = (int) ($data['tahun_rentang'] ?? now()->format('Y'));

                if ($bulanMulai > $bulanSelesai) {
                    [$bulanMulai, $bulanSelesai] = [$bulanSelesai, $bulanMulai];
                }

                $startDate = Carbon::createFromDate($tahun, $bulanMulai, 1)->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::createFromDate($tahun, $bulanSelesai, 1)->endOfMonth()->format('Y-m-d');

                $query->whereBetween('tanggal', [$startDate, $endDate]);
            })(),
            'tahunan' => $query->whereYear('tanggal', (int) ($data['tahun_tunggal'] ?? now()->format('Y'))),
            default => null,
        };

        if (! empty($data['karyawan_id'])) {
            $query->where('karyawan_id', $data['karyawan_id']);
        }

        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        return $query->orderBy('tanggal', 'asc')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{label: string, file_suffix: string}
     */
    public function getPeriodeInfo(array $data): array
    {
        $tipe = $data['tipe_periode'] ?? 'bulanan';

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return match ($tipe) {
            'harian' => [
                'label' => 'Harian ('.Carbon::parse($data['tanggal'] ?? now())->translatedFormat('d F Y').')',
                'file_suffix' => 'harian_'.($data['tanggal'] ?? now()->format('Y-m-d')),
            ],
            'mingguan' => [
                'label' => 'Mingguan ('.Carbon::parse($data['tanggal_mulai'] ?? now()->startOfWeek())->translatedFormat('d M Y').' s.d. '.Carbon::parse($data['tanggal_selesai'] ?? now()->endOfWeek())->translatedFormat('d M Y').')',
                'file_suffix' => 'mingguan_'.($data['tanggal_mulai'] ?? now()->startOfWeek()->format('Y-m-d')).'_sd_'.($data['tanggal_selesai'] ?? now()->endOfWeek()->format('Y-m-d')),
            ],
            'bulanan' => [
                'label' => 'Bulan '.($namaBulan[(int) ($data['bulan'] ?? now()->format('n'))] ?? '').' '.($data['tahun'] ?? now()->format('Y')),
                'file_suffix' => 'bulan_'.strtolower($namaBulan[(int) ($data['bulan'] ?? now()->format('n'))] ?? 'unknown').'_'.($data['tahun'] ?? now()->format('Y')),
            ],
            'rentang_bulan' => (function () use ($data, $namaBulan): array {
                $bulanMulai = (int) ($data['bulan_mulai'] ?? 1);
                $bulanSelesai = (int) ($data['bulan_selesai'] ?? now()->format('n'));
                $tahun = $data['tahun_rentang'] ?? now()->format('Y');

                if ($bulanMulai > $bulanSelesai) {
                    [$bulanMulai, $bulanSelesai] = [$bulanSelesai, $bulanMulai];
                }

                return [
                    'label' => 'Rentang Bulan '.($namaBulan[$bulanMulai] ?? $bulanMulai).' - '.($namaBulan[$bulanSelesai] ?? $bulanSelesai).' '.$tahun,
                    'file_suffix' => 'rentang_bulan_'.$bulanMulai.'_sd_'.$bulanSelesai.'_'.$tahun,
                ];
            })(),
            'tahunan' => [
                'label' => 'Tahun '.($data['tahun_tunggal'] ?? now()->format('Y')),
                'file_suffix' => 'tahun_'.($data['tahun_tunggal'] ?? now()->format('Y')),
            ],
            default => [
                'label' => 'Semua Data',
                'file_suffix' => 'semua',
            ],
        };
    }

    /**
     * @param  Collection<int, Presensi>  $records
     * @param  array<string, mixed>  $data
     */
    public function downloadExcel(Collection $records, array $data): BinaryFileResponse
    {
        $info = $this->getPeriodeInfo($data);
        $periodeLabel = $info['label'];
        $fileName = 'laporan_presensi_'.$info['file_suffix'].'_'.now()->format('Ymd_His').'.xlsx';

        $tempFilePath = tempnam(sys_get_temp_dir(), 'presensi_').'.xlsx';

        $writer = new Writer;
        $writer->openToFile($tempFilePath);

        $titleStyle = (new Style)
            ->setFontBold()
            ->setFontSize(14)
            ->setFontColor(Color::rgb(30, 41, 59));

        $subTitleStyle = (new Style)
            ->setFontItalic()
            ->setFontSize(10)
            ->setFontColor(Color::rgb(100, 116, 139));

        $headerStyle = (new Style)
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor(Color::rgb(180, 83, 9));

        $dataStyle = (new Style)
            ->setFontSize(10);

        $summaryTitleStyle = (new Style)
            ->setFontBold()
            ->setFontSize(11)
            ->setBackgroundColor(Color::rgb(241, 245, 249));

        $summaryStyle = (new Style)
            ->setFontSize(10)
            ->setBackgroundColor(Color::rgb(248, 250, 252));

        // Judul Laporan
        $writer->addRow(Row::fromValues(['LAPORAN PRESENSI KARYAWAN - KAISAR BAKERY'], $titleStyle));
        $writer->addRow(Row::fromValues(['Periode: '.$periodeLabel], $subTitleStyle));
        $writer->addRow(Row::fromValues(['Dicetak pada: '.now()->translatedFormat('d F Y, H:i').' WIB'], $subTitleStyle));
        $writer->addRow(Row::fromValues([]));

        // Header Tabel
        $writer->addRow(Row::fromValues([
            'No',
            'Tanggal',
            'Hari',
            'Kode Alternatif',
            'Nama Karyawan',
            'Jabatan',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Metode Presensi',
            'Catatan',
        ], $headerStyle));

        $namaHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $no = 1;
        $countHadir = 0;
        $countIzin = 0;
        $countSakit = 0;
        $countAlpha = 0;

        foreach ($records as $record) {
            $status = strtolower($record->status ?? '');
            match ($status) {
                'hadir' => $countHadir++,
                'izin' => $countIzin++,
                'sakit' => $countSakit++,
                'alpha' => $countAlpha++,
                default => null,
            };

            $tgl = $record->tanggal ? Carbon::parse($record->tanggal) : null;
            $hari = $tgl ? ($namaHari[$tgl->format('l')] ?? $tgl->format('l')) : '-';
            $metode = ($record->metode === 'qr_dinamis') ? 'QR Dinamis' : 'Manual';

            $writer->addRow(Row::fromValues([
                $no++,
                $tgl ? $tgl->format('d/m/Y') : '-',
                $hari,
                $record->karyawan?->kode_karyawan ?? '-',
                $record->karyawan?->nama ?? '-',
                $record->karyawan?->jabatan ?? '-',
                $record->jam_masuk ?? '-',
                $record->jam_keluar ?? '-',
                ucfirst($record->status ?? '-'),
                $metode,
                $record->catatan ?? '-',
            ], $dataStyle));
        }

        // Summary
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['RINGKASAN REKAPITULASI PRESENSI', ''], $summaryTitleStyle));
        $writer->addRow(Row::fromValues(['Total Catatan Presensi', count($records)], $summaryStyle));
        $writer->addRow(Row::fromValues(['Total Hadir', $countHadir], $summaryStyle));
        $writer->addRow(Row::fromValues(['Total Izin', $countIzin], $summaryStyle));
        $writer->addRow(Row::fromValues(['Total Sakit', $countSakit], $summaryStyle));
        $writer->addRow(Row::fromValues(['Total Alpha', $countAlpha], $summaryStyle));
        $persen = count($records) > 0 ? round(($countHadir / count($records)) * 100, 1) : 0;
        $writer->addRow(Row::fromValues(['Persentase Kehadiran', $persen.'%'], $summaryStyle));

        $writer->close();

        return response()->download($tempFilePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
