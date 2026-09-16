<x-filament::section icon="heroicon-o-trophy">
    <x-slot name="heading">
        Hasil Akhir Perangkingan TOPSIS
    </x-slot>
    <x-slot name="description">
        Berdasarkan kedekatan relatif solusi ideal: Vi = D- / (D- + D+). Nilai tertinggi menjadi alternatif terbaik.
    </x-slot>

    <!-- Tampilan Podium 1 2 3 Karyawan Terbaik -->
    @include('filament.components.topsis-podium', ['rankings' => $topsisResult['rankings'] ?? []])

    <div style="overflow-x: auto; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead style="background: rgba(0,0,0,0.03); border-bottom: 2px solid rgba(0,0,0,0.08); font-size: 0.75rem; text-transform: uppercase; color: #6b7280;">
                <tr>
                    <th style="padding: 12px 16px; text-align: center;">Peringkat</th>
                    <th style="padding: 12px 16px;">Kode</th>
                    <th style="padding: 12px 16px;">Nama Alternatif (Karyawan)</th>
                    <th style="padding: 12px 16px;">Jabatan</th>
                    <th style="padding: 12px 16px; text-align: center;">Jarak D+</th>
                    <th style="padding: 12px 16px; text-align: center;">Jarak D-</th>
                    <th style="padding: 12px 16px; text-align: center;">Nilai Preferensi (Vi)</th>
                    <th style="padding: 12px 16px; text-align: center;">Kategori Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topsisResult['rankings'] ?? [] as $row)
                <tr @style([
                    'border-bottom: 1px solid rgba(0,0,0,0.05)',
                    'background: rgba(245, 158, 11, 0.08); font-weight: 600' => ($row['rank'] ?? 0) === 1,
                ])>
                    <td style="padding: 12px 16px; text-align: center;">
                        @if(($row['rank'] ?? 0) === 1)
                        <x-filament::badge color="warning">1 🏆</x-filament::badge>
                        @elseif(($row['rank'] ?? 0) === 2)
                        <x-filament::badge color="gray">2</x-filament::badge>
                        @elseif(($row['rank'] ?? 0) === 3)
                        <x-filament::badge color="primary">3</x-filament::badge>
                        @else
                        <span style="color: #6b7280; font-weight: 600;">{{ $row['rank'] }}</span>
                        @endif
                    </td>
                    <td style="padding: 12px 16px;">
                        <x-filament::badge color="primary" size="sm">{{ $row['kode'] }}</x-filament::badge>
                    </td>
                    <td style="padding: 12px 16px; font-weight: 600; color: #1f2937;">
                        {{ $row['nama'] }}
                    </td>
                    <td style="padding: 12px 16px; color: #6b7280;">
                        {{ $row['jabatan'] }}
                    </td>
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-size: 0.85rem;">
                        {{ number_format($row['d_plus'] ?? 0, 5) }}
                    </td>
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-size: 0.85rem;">
                        {{ number_format($row['d_minus'] ?? 0, 5) }}
                    </td>
                    <td style="padding: 12px 16px; text-align: center;">
                        <x-filament::badge color="success" size="lg">
                            <strong>{{ number_format($row['preference'] ?? 0, 4) }}</strong>
                        </x-filament::badge>
                    </td>
                    <td style="padding: 12px 16px; text-align: center;">
                        @if(($row['keterangan'] ?? '') === 'Kinerja Terbaik')
                        <x-filament::badge color="warning">{{ $row['keterangan'] }}</x-filament::badge>
                        @elseif(($row['keterangan'] ?? '') === 'Baik')
                        <x-filament::badge color="info">{{ $row['keterangan'] }}</x-filament::badge>
                        @else
                        <x-filament::badge color="gray">{{ $row['keterangan'] ?? '-' }}</x-filament::badge>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 24px; color: #9ca3af;">Belum ada data nilai pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament::section>
