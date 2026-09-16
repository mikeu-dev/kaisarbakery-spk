<x-filament::section icon="heroicon-o-sparkles">
    <x-slot name="heading">
        Tahap 4: Solusi Ideal Positif (A+) & Solusi Ideal Negatif (A-)
    </x-slot>
    <x-slot name="description">
        Kriteria Benefit: Max untuk A+, Min untuk A-. Kriteria Cost: Min untuk A+, Max untuk A-.
    </x-slot>

    <div style="overflow-x: auto; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead style="background: rgba(0,0,0,0.03); border-bottom: 2px solid rgba(0,0,0,0.08); font-size: 0.75rem; text-transform: uppercase; color: #6b7280;">
                <tr>
                    <th style="padding: 12px 16px;">Solusi Ideal</th>
                    @foreach($topsisResult['kriterias'] ?? [] as $crit)
                    <th style="padding: 12px 16px; text-align: center;">
                        <strong>{{ $crit['kode'] }}</strong> ({{ ucfirst($crit['jenis'] ?? '') }})
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr style="background: rgba(16, 185, 129, 0.08); border-bottom: 1px solid rgba(16, 185, 129, 0.2);">
                    <td style="padding: 12px 16px; font-weight: 700; color: #047857;">
                        Solusi Ideal Positif (A+)
                    </td>
                    @foreach($topsisResult['kriterias'] ?? [] as $crit)
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-weight: 700; color: #047857;">
                        {{ number_format($topsisResult['ideal_positive'][$crit['kode']] ?? 0, 4) }}
                    </td>
                    @endforeach
                </tr>
                <tr style="background: rgba(239, 68, 68, 0.08); border-bottom: 1px solid rgba(239, 68, 68, 0.2);">
                    <td style="padding: 12px 16px; font-weight: 700; color: #b91c1c;">
                        Solusi Ideal Negatif (A-)
                    </td>
                    @foreach($topsisResult['kriterias'] ?? [] as $crit)
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-weight: 700; color: #b91c1c;">
                        {{ number_format($topsisResult['ideal_negative'][$crit['kode']] ?? 0, 4) }}
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</x-filament::section>
