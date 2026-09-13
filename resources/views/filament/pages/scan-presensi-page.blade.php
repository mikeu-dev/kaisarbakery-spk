<x-filament-panels::page>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: start;">

        <!-- Left Column: Camera Scanner & Manual Input -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <x-filament::section icon="heroicon-o-camera">
                <x-slot name="heading">
                    Pemindai QR Presensi
                </x-slot>
                <x-slot name="description">
                    Arahkan kamera perangkat ke kode QR dinamis di layar kasir
                </x-slot>

                <!-- Camera Container -->
                <div x-data="{
                    scanner: null,
                    isScanning: false,
                    startScanner() {
                        if (typeof Html5Qrcode === 'undefined') {
                            alert('Pustaka kamera sedang dimuat, silakan coba 2 detik lagi...');
                            return;
                        }
                        this.isScanning = true;
                        this.scanner = new Html5Qrcode('qr-reader');
                        this.scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 220, height: 220 } },
                            (decodedText) => {
                                this.stopScanner();
                                $wire.processScan(decodedText);
                            },
                            (errorMessage) => {}
                        ).catch(err => {
                            this.isScanning = false;
                            console.error('Camera error: ', err);
                            alert('Tidak dapat mengakses kamera: ' + (err?.message || err) + '. Anda dapat menggunakan opsi Input Token Manual di bawah.');
                        });
                    },
                    stopScanner() {
                        if (this.scanner && this.isScanning) {
                            this.scanner.stop().then(() => {
                                this.isScanning = false;
                            }).catch(err => {
                                this.isScanning = false;
                            });
                        }
                    }
                }" style="display: flex; flex-direction: column; align-items: center; padding: 12px 0;">

                    <div id="qr-reader" style="width: 100%; max-width: 360px; min-height: 280px; border-radius: 12px; border: 2px dashed #f59e0b; overflow: hidden; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.02); position: relative;">
                        <template x-if="!isScanning">
                            <div style="text-align: center; padding: 24px; color: #6b7280;">
                                <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                                    <x-filament::icon icon="heroicon-o-viewfinder-circle" style="width: 56px; height: 56px; color: #f59e0b;" />
                                </div>
                                <div style="font-weight: 600; font-size: 0.95rem; color: #374151;">Kamera Belum Aktif</div>
                                <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">Klik tombol di bawah untuk menyalakan kamera perangkat</div>
                            </div>
                        </template>
                    </div>

                    <!-- Scanner Controls -->
                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <span x-show="!isScanning">
                            <x-filament::button color="warning" icon="heroicon-m-camera" @click="startScanner()">
                                Buka Kamera & Pindai QR
                            </x-filament::button>
                        </span>

                        <span x-show="isScanning" style="display: none;">
                            <x-filament::button color="danger" icon="heroicon-m-stop" @click="stopScanner()">
                                Matikan Kamera
                            </x-filament::button>
                        </span>
                    </div>
                </div>

                <!-- Fallback / Manual Token Input -->
                <x-slot name="footer">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 8px;">
                            Alternatif: Masukkan Token QR Manual
                        </div>
                        <form wire:submit="submitManual" style="display: flex; gap: 8px; align-items: center;">
                            <div style="flex: 1;">
                                <x-filament::input.wrapper>
                                    <x-filament::input
                                        type="text"
                                        wire:model="manualToken"
                                        placeholder="Contoh: KAISAR:29821909:12f76abfc88982eb"
                                        required
                                    />
                                </x-filament::input.wrapper>
                            </div>
                            <x-filament::button type="submit" color="gray" icon="heroicon-m-check">
                                Verifikasi
                            </x-filament::button>
                        </form>
                    </div>
                </x-slot>
            </x-filament::section>
        </div>

        <!-- Right Column: Profil Karyawan & Status Kehadiran -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Profil Karyawan -->
            <x-filament::section icon="heroicon-o-user">
                <x-slot name="heading">
                    Identitas Karyawan
                </x-slot>

                @if(auth()->user()->isAdmin())
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #6b7280; margin-bottom: 6px;">
                            Mode Admin (Pilih Karyawan yang Melakukan Absensi):
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="selectedKaryawanId">
                                @foreach(\App\Models\Karyawan::orderBy('kode_karyawan')->get() as $k)
                                    <option value="{{ $k->id }}">{{ $k->kode_karyawan }} - {{ $k->nama }} ({{ $k->jabatan }})</option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                @else
                    @if(auth()->user()->karyawan)
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(245, 158, 11, 0.08); border-radius: 10px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #f59e0b; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem;">
                                {{ substr(auth()->user()->karyawan->nama, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.95rem;">{{ auth()->user()->karyawan->nama }}</div>
                                <div style="font-size: 0.8rem; color: #6b7280;">{{ auth()->user()->karyawan->jabatan }} &bull; {{ auth()->user()->karyawan->kode_karyawan }}</div>
                            </div>
                        </div>
                    @endif
                @endif
            </x-filament::section>

            <!-- Status Kehadiran Hari Ini -->
            <x-filament::section icon="heroicon-o-calendar-days">
                <x-slot name="heading">
                    Status Presensi Hari Ini
                </x-slot>
                <x-slot name="headerEnd">
                    <x-filament::badge color="gray" size="sm">
                        {{ \Carbon\Carbon::today()->isoFormat('dddd, D MMMM Y') }}
                    </x-filament::badge>
                </x-slot>

                @if($todayPresensi)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="padding: 14px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #047857;">Status Kehadiran</span>
                                <x-filament::badge color="success">
                                    {{ strtoupper($todayPresensi['status']) }}
                                </x-filament::badge>
                            </div>
                            <div style="font-size: 0.8rem; color: #059669;">{{ $todayPresensi['catatan'] }}</div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div style="padding: 12px; border-radius: 10px; background: rgba(0,0,0,0.03); text-align: center; border: 1px solid rgba(0,0,0,0.06);">
                                <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 4px;">Jam Masuk</div>
                                <div style="font-size: 1.25rem; font-weight: 800; font-family: monospace; color: #1f2937;">
                                    {{ $todayPresensi['jam_masuk'] }}
                                </div>
                            </div>

                            <div style="padding: 12px; border-radius: 10px; background: rgba(0,0,0,0.03); text-align: center; border: 1px solid rgba(0,0,0,0.06);">
                                <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 4px;">Jam Pulang</div>
                                <div style="font-size: 1.25rem; font-weight: 800; font-family: monospace; color: #1f2937;">
                                    {{ $todayPresensi['jam_keluar'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 24px 12px;">
                        <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                            <x-filament::icon icon="heroicon-o-clock" style="width: 48px; height: 48px; color: #f59e0b;" />
                        </div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #374151;">Belum Presensi Hari Ini</div>
                        <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">
                            Silakan scan kode QR di layar kasir untuk mencatat jam masuk kerja Anda.
                        </div>
                    </div>
                @endif
            </x-filament::section>

        </div>

    </div>

    <!-- Load html5-qrcode script -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</x-filament-panels::page>
