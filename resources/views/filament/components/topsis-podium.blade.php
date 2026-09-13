@php
    $rank1 = $rankings[0] ?? null;
    $rank2 = $rankings[1] ?? null;
    $rank3 = $rankings[2] ?? null;
@endphp

@if($rank1 && $rank2 && $rank3)
    <div style="margin-bottom: 28px; padding: 24px 16px 0 16px; background: rgba(245, 158, 11, 0.03); border-radius: 16px; border: 1px solid rgba(245, 158, 11, 0.15);">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                <x-filament::badge color="warning" icon="heroicon-m-sparkles" size="sm">
                    PODIUM JUARA TOPSIS
                </x-filament::badge>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #1f2937; margin: 0;">
                Karyawan Kinerja Terbaik (Podium 1 - 2 - 3)
            </h3>
            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 4px;">
                Berdasarkan kalkulasi nilai kedekatan relatif solusi ideal (Vi) tertinggi
            </p>
        </div>

        <div style="display: flex; justify-content: center; align-items: flex-end; gap: 12px; max-width: 640px; margin: 0 auto;">
            
            <!-- PODIUM 2 (SILVER - JUARA 2) -->
            <div style="flex: 1; max-width: 190px; display: flex; flex-direction: column; align-items: center; text-align: center;">
                <div style="margin-bottom: 10px; display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 1.6rem; line-height: 1; margin-bottom: 4px;">🥈</div>
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: #f8fafc; border: 3px solid #94a3b8; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.15rem; color: #475569; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
                        {{ substr($rank2['nama'] ?? $rank2->karyawan?->nama ?? '', 0, 1) }}
                    </div>
                    <div style="font-weight: 700; font-size: 0.85rem; color: #1f2937; margin-top: 6px; line-height: 1.2;">
                        {{ $rank2['nama'] ?? $rank2->karyawan?->nama ?? '-' }}
                    </div>
                    <div style="font-size: 0.7rem; color: #64748b; margin: 2px 0 4px 0;">
                        {{ $rank2['jabatan'] ?? $rank2->karyawan?->jabatan ?? '-' }}
                    </div>
                    <x-filament::badge color="gray" size="sm">
                        Vi: {{ number_format($rank2['preference'] ?? $rank2->nilai_preferensi ?? 0, 4) }}
                    </x-filament::badge>
                </div>
                <!-- Pillar 2 Base -->
                <div style="width: 100%; height: 120px; background: linear-gradient(180deg, #94a3b8 0%, #64748b 100%); border-radius: 12px 12px 0 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 -2px 10px rgba(100,116,139,0.2);">
                    <span style="font-size: 2.5rem; font-weight: 900; line-height: 1; opacity: 0.95;">2</span>
                    <span style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; margin-top: 4px; opacity: 0.9;">PERINGKAT 2</span>
                </div>
            </div>

            <!-- PODIUM 1 (GOLD - JUARA 1) -->
            <div style="flex: 1.15; max-width: 220px; display: flex; flex-direction: column; align-items: center; text-align: center;">
                <div style="margin-bottom: 10px; display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 2.25rem; line-height: 1; margin-bottom: 2px; filter: drop-shadow(0 2px 6px rgba(217,119,6,0.4));">👑</div>
                    <div style="width: 66px; height: 66px; border-radius: 50%; background: #fef3c7; border: 4px solid #f59e0b; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.5rem; color: #b45309; box-shadow: 0 6px 16px rgba(217,119,6,0.35);">
                        {{ substr($rank1['nama'] ?? $rank1->karyawan?->nama ?? '', 0, 1) }}
                    </div>
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1f2937; margin-top: 6px; line-height: 1.2;">
                        {{ $rank1['nama'] ?? $rank1->karyawan?->nama ?? '-' }}
                    </div>
                    <div style="font-size: 0.75rem; color: #d97706; font-weight: 600; margin: 2px 0 4px 0;">
                        {{ $rank1['jabatan'] ?? $rank1->karyawan?->jabatan ?? '-' }}
                    </div>
                    <x-filament::badge color="warning" size="md">
                        <strong>Vi: {{ number_format($rank1['preference'] ?? $rank1->nilai_preferensi ?? 0, 4) }}</strong>
                    </x-filament::badge>
                </div>
                <!-- Pillar 1 Base -->
                <div style="width: 100%; height: 165px; background: linear-gradient(180deg, #f59e0b 0%, #b45309 100%); border-radius: 16px 16px 0 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 -4px 15px rgba(217,119,6,0.35);">
                    <div style="font-size: 1.2rem; margin-bottom: -4px;">🏆</div>
                    <span style="font-size: 3.5rem; font-weight: 900; line-height: 1;">1</span>
                    <span style="font-size: 0.75rem; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase; margin-top: 4px;">JUARA 1</span>
                </div>
            </div>

            <!-- PODIUM 3 (BRONZE - JUARA 3) -->
            <div style="flex: 1; max-width: 190px; display: flex; flex-direction: column; align-items: center; text-align: center;">
                <div style="margin-bottom: 10px; display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 1.6rem; line-height: 1; margin-bottom: 4px;">🥉</div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #fff7ed; border: 3px solid #b45309; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; color: #78350f; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
                        {{ substr($rank3['nama'] ?? $rank3->karyawan?->nama ?? '', 0, 1) }}
                    </div>
                    <div style="font-weight: 700; font-size: 0.85rem; color: #1f2937; margin-top: 6px; line-height: 1.2;">
                        {{ $rank3['nama'] ?? $rank3->karyawan?->nama ?? '-' }}
                    </div>
                    <div style="font-size: 0.7rem; color: #9a3412; margin: 2px 0 4px 0;">
                        {{ $rank3['jabatan'] ?? $rank3->karyawan?->jabatan ?? '-' }}
                    </div>
                    <x-filament::badge color="primary" size="sm">
                        Vi: {{ number_format($rank3['preference'] ?? $rank3->nilai_preferensi ?? 0, 4) }}
                    </x-filament::badge>
                </div>
                <!-- Pillar 3 Base -->
                <div style="width: 100%; height: 95px; background: linear-gradient(180deg, #b45309 0%, #78350f 100%); border-radius: 12px 12px 0 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 -2px 10px rgba(120,53,15,0.2);">
                    <span style="font-size: 2.25rem; font-weight: 900; line-height: 1; opacity: 0.95;">3</span>
                    <span style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; margin-top: 4px; opacity: 0.9;">PERINGKAT 3</span>
                </div>
            </div>

        </div>
    </div>
@endif
