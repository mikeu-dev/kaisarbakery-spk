<x-filament::section icon="heroicon-o-arrows-right-left">
    <x-slot name="heading">
        Tahap 5: Jarak Euclidean ke Solusi Ideal (D+ & D-)
    </x-slot>
    <x-slot name="description">
        Rumus: D+ = √∑(yij - yj+)² &bull; D- = √∑(yij - yj-)²
    </x-slot>

    <div style="overflow-x: auto; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead style="background: rgba(0,0,0,0.03); border-bottom: 2px solid rgba(0,0,0,0.08); font-size: 0.75rem; text-transform: uppercase; color: #6b7280;">
                <tr>
                    <th style="padding: 12px 16px;">Alternatif</th>
                    <th style="padding: 12px 16px; text-align: center;">Jarak ke Solusi Ideal Positif (D+)</th>
                    <th style="padding: 12px 16px; text-align: center;">Jarak ke Solusi Ideal Negatif (D-)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topsisResult['karyawans'] ?? [] as $k)
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <td style="padding: 12px 16px; font-weight: 600;">
                        {{ $k['kode'] }} - {{ $k['nama'] }}
                    </td>
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; color: #047857; font-weight: 600;">
                        {{ number_format($topsisResult['distances'][$k['id']]['d_plus'] ?? 0, 5) }}
                    </td>
                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; color: #b91c1c; font-weight: 600;">
                        {{ number_format($topsisResult['distances'][$k['id']]['d_minus'] ?? 0, 5) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::section>
