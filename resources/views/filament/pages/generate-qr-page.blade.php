<x-filament-panels::page>
    <div wire:poll.5s="refreshQr" x-data="{
        remaining: @entangle('secondsRemaining'),
        init() {
            setInterval(() => {
                if (this.remaining > 0) {
                    this.remaining--;
                } else {
                    this.remaining = 60;
                }
            }, 1000);
        }
    }" style="max-width: 600px; margin: 0 auto; width: 100%;">

        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 1.25rem;">
                    <span>🥐</span>
                    <span style="font-weight: 800; letter-spacing: 0.05em;">KAISAR BAKERY</span>
                    <span>🥖</span>
                </div>
            </x-slot>

            <x-slot name="description">
                <div style="text-align: center; margin-top: 4px;">
                    <div style="font-weight: 600; color: #d97706; margin-bottom: 6px;">SISTEM PRESENSI DINAMIS DIGITAL</div>
                    <x-filament::badge color="success" icon="heroicon-m-shield-check" size="sm">
                        Anti Titip Absen &bull; Enkripsi Time-Based
                    </x-filament::badge>
                </div>
            </x-slot>

            <div style="display: flex; flex-direction: column; align-items: center; padding: 16px 0; text-align: center;">
                <!-- QR Code Box -->
                <div style="background: #ffffff; padding: 16px; border-radius: 1rem; box-shadow: 0 4px 20px rgba(217, 119, 6, 0.15); border: 3px solid #f59e0b; display: inline-block;">
                    <img src="{{ $qrSvg }}" alt="QR Code Presensi Dinamis" style="width: 250px; height: 250px; display: block; margin: 0 auto; object-fit: contain;" />
                    <div style="margin-top: 8px;">
                        <x-filament::badge color="warning" size="sm">
                            SCAN DISINI UNTUK PRESENSI
                        </x-filament::badge>
                    </div>
                </div>

                <!-- Countdown and Refresh -->
                <div style="margin-top: 24px; width: 100%; max-width: 320px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">
                        <span style="color: #6b7280;">Pembaruan Kode:</span>
                        <x-filament::badge color="primary">
                            <span x-text="remaining + ' detik'"></span>
                        </x-filament::badge>
                    </div>
                    
                    <div style="width: 100%; height: 8px; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(to right, #f59e0b, #d97706); transition: width 1s linear; border-radius: 9999px;"
                             :style="'width: ' + ((remaining / 60) * 100) + '%'"></div>
                    </div>
                </div>

                <!-- Token and Timestamp Details -->
                <div style="margin-top: 20px; font-size: 0.8rem; color: #6b7280;">
                    <div style="font-family: monospace; font-size: 0.85rem; background: rgba(0,0,0,0.04); padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 6px;">
                        Token: <strong style="color: #1f2937;">{{ $token }}</strong>
                    </div>
                    <div>Terakhir diperbarui: <strong>{{ $lastUpdated }}</strong></div>
                </div>

                <div style="margin-top: 16px;">
                    <x-filament::button wire:click="refreshQr" icon="heroicon-m-arrow-path" color="gray" size="sm">
                        Segarkan QR Sekarang
                    </x-filament::button>
                </div>
            </div>

            <x-slot name="footer">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; text-align: center; font-size: 0.75rem;">
                    <div style="padding: 10px; border-radius: 8px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2);">
                        <strong style="color: #d97706; display: block; margin-bottom: 2px;">Langkah 1</strong>
                        <span>Login akun Karyawan</span>
                    </div>
                    <div style="padding: 10px; border-radius: 8px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2);">
                        <strong style="color: #d97706; display: block; margin-bottom: 2px;">Langkah 2</strong>
                        <span>Buka Menu Scan Presensi</span>
                    </div>
                    <div style="padding: 10px; border-radius: 8px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2);">
                        <strong style="color: #d97706; display: block; margin-bottom: 2px;">Langkah 3</strong>
                        <span>Arahkan kamera ke QR</span>
                    </div>
                </div>
            </x-slot>
        </x-filament::section>

    </div>
</x-filament-panels::page>
