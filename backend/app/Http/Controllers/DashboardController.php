<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\Karyawan $karyawan */
        $karyawan = Auth::user();
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();

        $absensiHariIni = Absensi::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        $bulanIni = Absensi::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->whereNotNull('jam_masuk')
            ->get();

        return view('dashboard.index', [
            'karyawan' => $karyawan,
            'absensiHariIni' => $absensiHariIni,
            'hadirBulanIni' => $bulanIni->count(),
            'terlambatBulanIni' => $bulanIni->where('status', 'Terlambat')->count(),
            'now' => $now,
        ]);
    }
}
