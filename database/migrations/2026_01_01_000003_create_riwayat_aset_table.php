<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('riwayat_aset', function (Blueprint $t) {
            $t->id();
            $t->foreignId('aset_id')->constrained('aset')->cascadeOnDelete();
            $t->string('jenis', 30);
            $t->date('tanggal');
            $t->text('keterangan');
            $t->string('dicatat_oleh', 100)->nullable();
            $t->timestamps();

            $t->index(['aset_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_aset');
    }
};
