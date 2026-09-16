<x-filament::section icon="heroicon-o-scale">
    <x-slot name="heading">
        Tahap 3: Matriks Ternormalisasi Terbobot (Y)
    </x-slot>
    <x-slot name="description">
        Dihitung dengan rumus: yij = wj * rij (perkalian matriks normalisasi dengan bobot preferensi kriteria)
    </x-slot>

    <div style="overflow-x: auto; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead style="background: rgba(0,0,0,0.03); border-bottom: 2px solid rgba(0,0,0,0.08); font-size: 0.75rem; text-transform: uppercase; color: #6b7280;">
                <tr>
                    <th style="padding: 12px 16px;">Alternatif</th>
                    @foreach($topsisResult['kriterias'] ?? [] as $crit)
                    <th style="padding: 12px 16px; text-align: center;">
                        <strong>{{ $crit['kode'] }}</strong> (w = {{ $crit['bobot'] ?? 0 }})
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
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-weight: 600;">
                        {{ number_format($topsisResult['matrix_y'][$k['id']][$crit['kode']] ?? 0, 4) }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::section>
