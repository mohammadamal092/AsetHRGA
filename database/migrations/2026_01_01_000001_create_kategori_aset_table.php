<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kategori_aset', function (Blueprint $t) {
            $t->id();
            $t->string('nama', 100)->unique();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_aset');
    }
};
