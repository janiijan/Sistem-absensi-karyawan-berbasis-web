<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'durasi_menit',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'durasi_menit' => 'integer',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function getDurasiLabelAttribute(): string
    {
        if ($this->durasi_menit === null) {
            return '-';
        }

        $hours = intdiv($this->durasi_menit, 60);
        $minutes = $this->durasi_menit % 60;

        return "{$hours} jam {$minutes} menit";
    }
}
