<x-filament::section icon="heroicon-o-table-cells">
    <x-slot name="heading">
        Tahap 1: Matriks Keputusan Awal (X)
    </x-slot>
    <x-slot name="description">
        Nilai riil evaluasi setiap alternatif karyawan pada 5 kriteria (Presensi C1, Kualitas C2, Kuantitas C3, Kedisiplinan C4, Kerjasama C5)
    </x-slot>

    <div style="overflow-x: auto; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead style="background: rgba(0,0,0,0.03); border-bottom: 2px solid rgba(0,0,0,0.08); font-size: 0.75rem; text-transform: uppercase; color: #6b7280;">
                <tr>
                    <th style="padding: 12px 16px;">Alternatif</th>
                    @foreach($topsisResult['kriterias'] ?? [] as $crit)
                    <th style="padding: 12px 16px; text-align: center;">
                        <div><strong>{{ $crit['kode'] }}</strong></div>
                        <div style="font-size: 0.65rem; color: #9ca3af; text-transform: none;">{{ $crit['nama'] }} ({{ ($crit['bobot'] ?? 0) * 100 }}%)</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($topsisResult['karyawans'] ?? [] as $k)
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <td style="padding: 12px 16px; font-weight: 600;">
                        {{ $k['kode'] }} - {{ $k['nama'] }}
                    </td>
                    @foreach($topsisResult['kriterias'] ?? [] as $crit)
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-size: 0.9rem;">
                        {{ number_format($topsisResult['matrix_x'][$k['id']][$crit['kode']] ?? 0, 1) }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
                <tr style="background: rgba(245, 158, 11, 0.08); font-weight: 700; border-top: 2px solid rgba(245, 158, 11, 0.3);">
                    <td style="padding: 12px 16px; color: #d97706;">
                        Pembagi (Divisor √∑x²)
                    </td>
                    @foreach($topsisResult['kriterias'] ?? [] as $crit)
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; color: #d97706;">
                        {{ number_format($topsisResult['divisors'][$crit['kode']] ?? 0, 4) }}
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</x-filament::section>
