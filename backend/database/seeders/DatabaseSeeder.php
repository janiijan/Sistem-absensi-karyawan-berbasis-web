<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $andi = Karyawan::query()->updateOrCreate(
            ['id_karyawan' => 'KRY001'],
            [
                'nama' => 'Andi Pratama',
                'password' => Hash::make('password123'),
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]
        );

        Karyawan::query()->updateOrCreate(
            ['id_karyawan' => 'KRY002'],
            [
                'nama' => 'Siti Rahma',
                'password' => Hash::make('password123'),
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]
        );

        if (Absensi::query()->where('karyawan_id', $andi->id)->exists()) {
            return;
        }

        $now = now('Asia/Jakarta');
        for ($i = 1; $i <= 14; $i++) {
            $date = $now->copy()->subDays($i);
            if ($date->isWeekend()) {
                continue;
            }

            $late = $i % 5 === 0;
            $jamMasuk = $late ? '08:24:00' : '07:52:00';
            $jamPulang = '17:05:00';
            $masuk = $date->copy()->setTimeFromTimeString($jamMasuk);
            $pulang = $date->copy()->setTimeFromTimeString($jamPulang);

            Absensi::create([
                'karyawan_id' => $andi->id,
                'tanggal' => $date->toDateString(),
                'jam_masuk' => $jamMasuk,
                'jam_pulang' => $jamPulang,
                'status' => $late ? 'Terlambat' : 'Tepat Waktu',
                'durasi_menit' => $masuk->diffInMinutes($pulang),
            ]);
        }
    }
}
