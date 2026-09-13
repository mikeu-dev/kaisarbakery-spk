<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class DynamicQrService
{
    /**
     * Duration of validity for dynamic QR code in seconds.
     */
    public const WINDOW_SECONDS = 60;

    /**
     * Generate the time window index.
     */
    public function getCurrentWindow(?int $time = null): int
    {
        $t = $time ?? time();

        return (int) floor($t / self::WINDOW_SECONDS);
    }

    /**
     * Generate dynamic token for QR code.
     */
    public function generateToken(?int $window = null): string
    {
        $win = $window ?? $this->getCurrentWindow();
        $secret = config('app.key', 'kaisar-secret-key-12345');
        $hash = substr(hash_hmac('sha256', "KAISAR_QR_{$win}", $secret), 0, 16);

        return "KAISAR:{$win}:{$hash}";
    }

    /**
     * Generate SVG data URI of the QR Code for dynamic token.
     */
    public function generateQrCodeSvg(?string $token = null): string
    {
        $payload = $token ?? $this->generateToken();

        $options = new QROptions([
            'version' => 5,
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_L,
            'addQuietzone' => true,
            'svgUseFill' => true,
            'scale' => 6,
        ]);

        return (new QRCode($options))->render($payload);
    }

    /**
     * Validate scanned token.
     * Allows current window and previous window (tolerating clock skew and transmission delay).
     */
    public function validateToken(string $token): bool
    {
        $parts = explode(':', trim($token));
        if (count($parts) !== 3 || $parts[0] !== 'KAISAR') {
            return false;
        }

        $win = (int) $parts[1];
        $hash = $parts[2];

        $currentWin = $this->getCurrentWindow();

        // Check if window is current or immediately preceding (within 60s tolerance)
        if ($win !== $currentWin && $win !== ($currentWin - 1)) {
            return false;
        }

        $expectedToken = $this->generateToken($win);
        $expectedParts = explode(':', $expectedToken);

        return hash_equals($expectedParts[2], $hash);
    }

    /**
     * Process attendance from scanned token for a given employee.
     *
     * @return array{success: bool, type: string, message: string, presensi?: Presensi}
     */
    public function recordAttendance(Karyawan $karyawan, string $token): array
    {
        if (! $this->validateToken($token)) {
            return [
                'success' => false,
                'type' => 'invalid_token',
                'message' => 'QR Code tidak valid atau sudah kedaluwarsa. Silakan scan ulang QR Code terbaru.',
            ];
        }

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        $presensi = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if (! $presensi) {
            // First scan of the day -> Clock In (Jam Masuk)
            $presensi = Presensi::create([
                'karyawan_id' => $karyawan->id,
                'tanggal' => $today,
                'jam_masuk' => $nowTime,
                'status' => 'hadir',
                'metode' => 'qr_dinamis',
                'token_validasi' => $token,
                'catatan' => 'Presensi masuk via QR Dinamis',
            ]);

            return [
                'success' => true,
                'type' => 'check_in',
                'message' => "Presensi masuk berhasil dicatat pada pukul {$nowTime}.",
                'presensi' => $presensi,
            ];
        }

        if (empty($presensi->jam_keluar)) {
            // Second scan of the day -> Clock Out (Jam Keluar)
            $presensi->update([
                'jam_keluar' => $nowTime,
                'catatan' => ($presensi->catatan ? $presensi->catatan.' | ' : '').'Presensi keluar via QR Dinamis',
            ]);

            return [
                'success' => true,
                'type' => 'check_out',
                'message' => "Presensi pulang berhasil dicatat pada pukul {$nowTime}.",
                'presensi' => $presensi,
            ];
        }

        // Already checked in and checked out
        return [
            'success' => false,
            'type' => 'already_completed',
            'message' => 'Anda sudah menyelesaikan presensi masuk dan pulang untuk hari ini.',
            'presensi' => $presensi,
        ];
    }
}
