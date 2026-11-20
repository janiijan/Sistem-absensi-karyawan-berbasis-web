<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    private const WORK_START = '08:00:00';

    public function index(): View
    {
        /** @var \App\Models\Karyawan $karyawan */
        $karyawan = Auth::user();
        $today = now('Asia/Jakarta')->toDateString();
        $absensi = Absensi::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        return view('absensi.index', [
            'karyawan' => $karyawan,
            'absensi' => $absensi,
            'now' => now('Asia/Jakarta'),
        ]);
    }

    public function masuk(Request $request): RedirectResponse
    {
        /** @var \App\Models\Karyawan $karyawan */
        $karyawan = Auth::user();
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();
        $jamMasuk = $now->format('H:i:s');

        $existing = Absensi::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing?->jam_masuk) {
            return back()->with('error', 'Absen masuk hari ini sudah tercatat.');
        }

        $status = $jamMasuk > self::WORK_START ? 'Terlambat' : 'Tepat Waktu';

        DB::transaction(function () use ($karyawan, $today, $jamMasuk, $status, $existing) {
            if ($existing) {
                $existing->update([
                    'jam_masuk' => $jamMasuk,
                    'status' => $status,
                ]);
            } else {
                Absensi::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $today,
                    'jam_masuk' => $jamMasuk,
                    'status' => $status,
                ]);
            }
        });

        return back()->with('success', "Absen masuk berhasil · {$status}");
    }

    public function pulang(Request $request): RedirectResponse
    {
        /** @var \App\Models\Karyawan $karyawan */
        $karyawan = Auth::user();
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();
        $jamPulang = $now->format('H:i:s');

        try {
            DB::transaction(function () use ($karyawan, $today, $jamPulang, $now) {
                $absensi = Absensi::query()
                    ->where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $today)
                    ->lockForUpdate()
                    ->first();

                if (! $absensi || ! $absensi->jam_masuk) {
                    throw new \RuntimeException('Anda belum absen masuk hari ini.');
                }

                if ($absensi->jam_pulang) {
                    throw new \RuntimeException('Absen pulang hari ini sudah tercatat.');
                }

                $masuk = $now->copy()->setTimeFromTimeString($absensi->jam_masuk);
                $pulang = $now->copy()->setTimeFromTimeString($jamPulang);
                $durasi = max(0, (int) $masuk->diffInMinutes($pulang, false));

                $absensi->update([
                    'jam_pulang' => $jamPulang,
                    'durasi_menit' => $durasi,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Absen pulang berhasil tersimpan.');
    }
}
