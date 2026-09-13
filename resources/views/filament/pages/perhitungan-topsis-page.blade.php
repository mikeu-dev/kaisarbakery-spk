<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">

        <!-- Top Banner: Period Selector & Winner Spotlight -->
        <x-filament::section icon="heroicon-o-calendar">
            <x-slot name="heading">
                Periode Penilaian Kinerja Karyawan
            </x-slot>
            <x-slot name="description">
                Pilih periode untuk melihat kalkulasi 6 tahapan metode TOPSIS dan hasil perangkingan
            </x-slot>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; align-items: center;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 6px;">
                        Pilih Periode:
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="periode">
                            <option value="2026-09">September 2026 (Data Skripsi Bab IV)</option>
                            <option value="2026-08">Agustus 2026</option>
                            <option value="2026-07">Juli 2026</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                @if(!empty($topsisResult['rankings']))
                    @php $top = $topsisResult['rankings'][0]; @endphp
                    <div style="padding: 16px; border-radius: 12px; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #ffffff; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.25);">
                        <div style="font-size: 2.25rem; background: rgba(255, 255, 255, 0.2); padding: 8px 12px; border-radius: 10px; line-height: 1;">
                            🏆
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #fef3c7;">
                                Karyawan Kinerja Terbaik (Peringkat 1)
                            </div>
                            <div style="font-size: 1.15rem; font-weight: 800; margin: 2px 0;">
                                {{ $top['kode'] }} - {{ $top['nama'] }} ({{ $top['jabatan'] }})
                            </div>
                            <div style="font-size: 0.8rem; color: #fef3c7; display: flex; align-items: center; gap: 8px;">
                                <span>Nilai Preferensi (Vi): <strong style="color: #ffffff; font-size: 0.95rem;">{{ number_format($top['preference'], 4) }}</strong></span>
                                <x-filament::badge color="warning" size="sm">
                                    {{ $top['keterangan'] }}
                                </x-filament::badge>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Filament Tabs for TOPSIS Stages -->
        <x-filament::tabs>
            <x-filament::tabs.item :active="$activeTab === 'hasil_akhir'" wire:click="$set('activeTab', 'hasil_akhir')" icon="heroicon-m-trophy">
                1. Perangkingan Akhir (Vi)
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 'matriks_x'" wire:click="$set('activeTab', 'matriks_x')" icon="heroicon-m-table-cells">
                2. Matriks Keputusan (X)
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 'matriks_r'" wire:click="$set('activeTab', 'matriks_r')" icon="heroicon-m-calculator">
                3. Normalisasi Matriks (R)
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 'matriks_y'" wire:click="$set('activeTab', 'matriks_y')" icon="heroicon-m-scale">
                4. Matriks Terbobot (Y)
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 'solusi_ideal'" wire:click="$set('activeTab', 'solusi_ideal')" icon="heroicon-m-sparkles">
                5. Solusi Ideal (A+ / A-)
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 'jarak_ideal'" wire:click="$set('activeTab', 'jarak_ideal')" icon="heroicon-m-arrows-right-left">
                6. Jarak Euclidean (D+ / D-)
            </x-filament::tabs.item>
        </x-filament::tabs>

        <!-- TAB CONTENT 1: HASIL PERANGKINGAN AKHIR -->
        @if($activeTab === 'hasil_akhir')
            <x-filament::section icon="heroicon-o-trophy">
                <x-slot name="heading">
                    Hasil Akhir Perangkingan TOPSIS
                </x-slot>
                <x-slot name="description">
                    Berdasarkan kedekatan relatif solusi ideal: Vi = D- / (D- + D+). Nilai tertinggi menjadi alternatif terbaik.
                </x-slot>

                <!-- Tampilan Podium 1 2 3 Karyawan Terbaik -->
                @include('filament.components.topsis-podium', ['rankings' => $topsisResult['rankings']])

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
                            @forelse($topsisResult['rankings'] as $row)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05); {{ $row['rank'] === 1 ? 'background: rgba(245, 158, 11, 0.08); font-weight: 600;' : '' }}">
                                    <td style="padding: 12px 16px; text-align: center;">
                                        @if($row['rank'] === 1)
                                            <x-filament::badge color="warning">1 🏆</x-filament::badge>
                                        @elseif($row['rank'] === 2)
                                            <x-filament::badge color="gray">2</x-filament::badge>
                                        @elseif($row['rank'] === 3)
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
                                        {{ number_format($row['d_plus'], 5) }}
                                    </td>
                                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-size: 0.85rem;">
                                        {{ number_format($row['d_minus'], 5) }}
                                    </td>
                                    <td style="padding: 12px 16px; text-align: center;">
                                        <x-filament::badge color="success" size="lg">
                                            <strong>{{ number_format($row['preference'], 4) }}</strong>
                                        </x-filament::badge>
                                    </td>
                                    <td style="padding: 12px 16px; text-align: center;">
                                        @if($row['keterangan'] === 'Kinerja Terbaik')
                                            <x-filament::badge color="warning">{{ $row['keterangan'] }}</x-filament::badge>
                                        @elseif($row['keterangan'] === 'Baik')
                                            <x-filament::badge color="info">{{ $row['keterangan'] }}</x-filament::badge>
                                        @else
                                            <x-filament::badge color="gray">{{ $row['keterangan'] }}</x-filament::badge>
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
        @endif

        <!-- TAB CONTENT 2: MATRIKS KEPUTUSAN X -->
        @if($activeTab === 'matriks_x')
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
                                @foreach($topsisResult['kriterias'] as $crit)
                                    <th style="padding: 12px 16px; text-align: center;">
                                        <div><strong>{{ $crit['kode'] }}</strong></div>
                                        <div style="font-size: 0.65rem; color: #9ca3af; text-transform: none;">{{ $crit['nama'] }} ({{ $crit['bobot'] * 100 }}%)</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topsisResult['karyawans'] as $k)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td style="padding: 12px 16px; font-weight: 600;">
                                        {{ $k['kode'] }} - {{ $k['nama'] }}
                                    </td>
                                    @foreach($topsisResult['kriterias'] as $crit)
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
                                @foreach($topsisResult['kriterias'] as $crit)
                                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; color: #d97706;">
                                        {{ number_format($topsisResult['divisors'][$crit['kode']] ?? 0, 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <!-- TAB CONTENT 3: MATRIKS NORMALISASI R -->
        @if($activeTab === 'matriks_r')
            <x-filament::section icon="heroicon-o-calculator">
                <x-slot name="heading">
                    Tahap 2: Matriks Ternormalisasi (R)
                </x-slot>
                <x-slot name="description">
                    Dihitung dengan rumus: rij = xij / √∑xij²
                </x-slot>

                <div style="overflow-x: auto; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead style="background: rgba(0,0,0,0.03); border-bottom: 2px solid rgba(0,0,0,0.08); font-size: 0.75rem; text-transform: uppercase; color: #6b7280;">
                            <tr>
                                <th style="padding: 12px 16px;">Alternatif</th>
                                @foreach($topsisResult['kriterias'] as $crit)
                                    <th style="padding: 12px 16px; text-align: center;">
                                        <strong>{{ $crit['kode'] }}</strong> ({{ $crit['nama'] }})
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topsisResult['karyawans'] as $k)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td style="padding: 12px 16px; font-weight: 600;">
                                        {{ $k['kode'] }} - {{ $k['nama'] }}
                                    </td>
                                    @foreach($topsisResult['kriterias'] as $crit)
                                        <td style="padding: 12px 16px; text-align: center; font-family: monospace;">
                                            {{ number_format($topsisResult['matrix_r'][$k['id']][$crit['kode']] ?? 0, 4) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <!-- TAB CONTENT 4: MATRIKS TERBOBOT Y -->
        @if($activeTab === 'matriks_y')
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
                                @foreach($topsisResult['kriterias'] as $crit)
                                    <th style="padding: 12px 16px; text-align: center;">
                                        <strong>{{ $crit['kode'] }}</strong> (w = {{ $crit['bobot'] }})
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topsisResult['karyawans'] as $k)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td style="padding: 12px 16px; font-weight: 600;">
                                        {{ $k['kode'] }} - {{ $k['nama'] }}
                                    </td>
                                    @foreach($topsisResult['kriterias'] as $crit)
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
        @endif

        <!-- TAB CONTENT 5: SOLUSI IDEAL A+ & A- -->
        @if($activeTab === 'solusi_ideal')
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
                                @foreach($topsisResult['kriterias'] as $crit)
                                    <th style="padding: 12px 16px; text-align: center;">
                                        <strong>{{ $crit['kode'] }}</strong> ({{ ucfirst($crit['jenis']) }})
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background: rgba(16, 185, 129, 0.08); border-bottom: 1px solid rgba(16, 185, 129, 0.2);">
                                <td style="padding: 12px 16px; font-weight: 700; color: #047857;">
                                    Solusi Ideal Positif (A+)
                                </td>
                                @foreach($topsisResult['kriterias'] as $crit)
                                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-weight: 700; color: #047857;">
                                        {{ number_format($topsisResult['ideal_positive'][$crit['kode']] ?? 0, 4) }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr style="background: rgba(239, 68, 68, 0.08); border-bottom: 1px solid rgba(239, 68, 68, 0.2);">
                                <td style="padding: 12px 16px; font-weight: 700; color: #b91c1c;">
                                    Solusi Ideal Negatif (A-)
                                </td>
                                @foreach($topsisResult['kriterias'] as $crit)
                                    <td style="padding: 12px 16px; text-align: center; font-family: monospace; font-weight: 700; color: #b91c1c;">
                                        {{ number_format($topsisResult['ideal_negative'][$crit['kode']] ?? 0, 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <!-- TAB CONTENT 6: JARAK IDEAL D+ & D- -->
        @if($activeTab === 'jarak_ideal')
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
                            @foreach($topsisResult['karyawans'] as $k)
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
        @endif

    </div>

    <!-- Print Stylesheet -->
    <style>
        @media print {
            aside, header, nav, .fi-topbar, .fi-sidebar, .fi-tabs, button, select {
                display: none !important;
            }
        }
    </style>
</x-filament-panels::page>
