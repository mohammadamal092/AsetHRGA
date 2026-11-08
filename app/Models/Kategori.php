<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori_aset';
    protected $fillable = ['nama'];

    public function aset(): HasMany
    {
        return $this->hasMany(Aset::class, 'kategori_id');
    }
}
