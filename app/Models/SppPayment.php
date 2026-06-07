<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SppPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'bulan',
        'tahun_ajaran',
        'nominal',
        'tanggal_bayar',
        'metode_bayar',
        'keterangan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($payment) {
            $santri = $payment->santri;
            if ($santri) {
                Kas::where('kategori', 'SPP')
                    ->where('nominal', $payment->nominal)
                    ->where(function ($query) use ($santri) {
                        $query->where('keterangan', 'like', "%(NIS: {$santri->nis})%")
                              ->orWhere('keterangan', 'like', "%a.n {$santri->nama}%");
                    })
                    ->delete();
            }
        });
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNamaBulanAttribute(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $bulan[$this->bulan] ?? '-';
    }
}
