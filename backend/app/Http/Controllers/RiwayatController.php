<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\Karyawan $karyawan */
        $karyawan = Auth::user();
        $now = now('Asia/Jakarta');

        $bulan = (int) $request->input('bulan', $now->month);
        $tahun = (int) $request->input('tahun', $now->year);

        $query = Absensi::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereNotNull('jam_masuk')
            ->orderByDesc('tanggal');

        $summaryBase = (clone $query)->get();
        $totalHadir = $summaryBase->count();
        $totalTepat = $summaryBase->where('status', 'Tepat Waktu')->count();
        $totalTerlambat = $summaryBase->where('status', 'Terlambat')->count();

        $riwayat = $query->paginate(10)->withQueryString();

        return view('riwayat.index', [
            'karyawan' => $karyawan,
            'riwayat' => $riwayat,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'totalHadir' => $totalHadir,
            'totalTepat' => $totalTepat,
            'totalTerlambat' => $totalTerlambat,
            'now' => $now,
        ]);
    }
}
