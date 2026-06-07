<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Santri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_wali',
        'no_hp_wali',
        'kelas',
        'tahun_masuk',
        'status',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($santri) {
            $santri->sppPayments->each(function ($payment) {
                $payment->delete();
            });
        });
    }

    public function sppPayments(): HasMany
    {
        return $this->hasMany(SppPayment::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'success',
            'alumni' => 'info',
            'keluar' => 'danger',
            default => 'secondary',
        };
    }
}
