<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatAset extends Model
{
    public const JENIS = [
        'pembelian'         => 'Pembelian / Pencatatan Awal',
        'pindah_lokasi'     => 'Pindah Lokasi',
        'serah_terima'      => 'Serah Terima / Ganti Pengguna',
        'perbaikan'         => 'Perbaikan / Servis',
        'perubahan_kondisi' => 'Perubahan Kondisi',
        'lainnya'           => 'Lainnya',
    ];

    protected $table = 'riwayat_aset';
    protected $fillable = ['aset_id', 'jenis', 'tanggal', 'keterangan', 'dicatat_oleh'];
    protected $casts = ['tanggal' => 'date'];

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class);
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }
}
