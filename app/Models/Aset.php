<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aset extends Model
{
    public const KONDISI = [
        'baik'         => 'Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat'  => 'Rusak Berat',
    ];

    public const STATUS = [
        'aktif'     => 'Aktif (Terpakai)',
        'dipinjam'  => 'Dipinjam',
        'perbaikan' => 'Dalam Perbaikan',
        'dihapus'   => 'Dihapus / Tidak Terpakai',
    ];

    protected $table = 'aset';

    protected $fillable = [
        'kode_aset', 'nama', 'kategori_id', 'merk', 'model', 'nomor_seri',
        'tanggal_pembelian', 'harga_beli', 'supplier', 'nomor_nota', 'nota_path',
        'masa_manfaat_tahun', 'nilai_sisa', 'kondisi', 'status',
        'departemen', 'lokasi', 'pengguna', 'tanggal_penghapusan', 'catatan',
    ];

    protected $casts = [
        'tanggal_pembelian'   => 'date',
        'tanggal_penghapusan' => 'date',
        'harga_beli'          => 'float',
        'nilai_sisa'          => 'float',
    ];

    protected static function booted(): void
    {
        // Kode aset otomatis: AST-2026-0001
        static::creating(function (Aset $m) {
            if (! $m->kode_aset) {
                $prefix = 'AST-' . date('Y') . '-';
                $last = static::where('kode_aset', 'like', $prefix . '%')->max('kode_aset');
                $n = $last ? ((int) substr($last, -4)) + 1 : 1;
                $m->kode_aset = $prefix . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatAset::class)->orderByDesc('tanggal')->orderByDesc('id');
    }

    /* ---------- Umur & penyusutan ---------- */

    /** Umur dihitung sampai hari ini, atau sampai tanggal penghapusan jika aset sudah tidak terpakai. */
    public function getTanggalAkhirUmurAttribute()
    {
        return ($this->status === 'dihapus' && $this->tanggal_penghapusan)
            ? $this->tanggal_penghapusan
            : now();
    }

    private function selisihUmur()
    {
        return $this->tanggal_pembelian->copy()->startOfDay()
            ->diff($this->tanggal_akhir_umur->copy()->startOfDay());
    }

    public function getUmurAttribute(): string
    {
        $d = $this->selisihUmur();
        return "{$d->y} tahun {$d->m} bulan {$d->d} hari";
    }

    public function getUmurBulanAttribute(): int
    {
        $d = $this->selisihUmur();
        return $d->y * 12 + $d->m;
    }

    public function getUmurHariAttribute(): int
    {
        return (int) abs($this->tanggal_pembelian->copy()->startOfDay()
            ->diffInDays($this->tanggal_akhir_umur->copy()->startOfDay()));
    }

    public function getSisaBulanAttribute(): int
    {
        return $this->masa_manfaat_tahun * 12 - $this->umur_bulan;
    }

    public function getTanggalAkhirManfaatAttribute()
    {
        return $this->tanggal_pembelian->copy()->addYears($this->masa_manfaat_tahun);
    }

    public function getPersenUmurAttribute(): int
    {
        $total = max(1, $this->masa_manfaat_tahun * 12);
        return (int) min(100, round($this->umur_bulan / $total * 100));
    }

    /** Nilai buku metode garis lurus per bulan. */
    public function getNilaiBukuAttribute(): float
    {
        $total = max(1, $this->masa_manfaat_tahun * 12);
        $pakai = min($this->umur_bulan, $total);
        $susut = ($this->harga_beli - $this->nilai_sisa) / $total * $pakai;
        return max($this->nilai_sisa, $this->harga_beli - $susut);
    }

    public function getStatusUmurAttribute(): string
    {
        if ($this->status === 'dihapus') return 'dihapus';
        if ($this->sisa_bulan <= 0) return 'habis';
        if ($this->sisa_bulan <= 6) return 'segera';
        return 'normal';
    }

    /* ---------- Label & warna ---------- */

    public function getKondisiLabelAttribute(): string
    {
        return self::KONDISI[$this->kondisi] ?? $this->kondisi;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getKondisiWarnaAttribute(): string
    {
        return ['baik' => 'success', 'rusak_ringan' => 'warning', 'rusak_berat' => 'danger'][$this->kondisi] ?? 'secondary';
    }

    public function getStatusWarnaAttribute(): string
    {
        return ['aktif' => 'primary', 'dipinjam' => 'info', 'perbaikan' => 'warning', 'dihapus' => 'secondary'][$this->status] ?? 'secondary';
    }

    public function getUmurWarnaAttribute(): string
    {
        return ['habis' => 'danger', 'segera' => 'warning', 'normal' => 'success', 'dihapus' => 'secondary'][$this->status_umur];
    }
}
