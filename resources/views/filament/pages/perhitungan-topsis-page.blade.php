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
                            <option value="2026-09">September 2026</option>
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
                            Karyawan Kinerja Terbaik
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

        <!-- Tabs Schema Component -->
        {{ $this->content }}

    </div>

    <!-- Print Stylesheet -->
    <style>
        @media print {

            aside,
            header,
            nav,
            .fi-topbar,
            .fi-sidebar,
            .fi-tabs,
            button,
            select {
                display: none !important;
            }
        }
    </style>
</x-filament-panels::page>