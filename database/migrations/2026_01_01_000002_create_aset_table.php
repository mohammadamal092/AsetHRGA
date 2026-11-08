<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $t) {
            $t->id();
            $t->string('kode_aset', 30)->unique();
            $t->string('nama', 150);
            $t->foreignId('kategori_id')->constrained('kategori_aset')->restrictOnDelete();
            $t->string('merk', 100)->nullable();
            $t->string('model', 100)->nullable();
            $t->string('nomor_seri', 100)->nullable();

            // Data pembelian awal
            $t->date('tanggal_pembelian');
            $t->decimal('harga_beli', 15, 2)->default(0);
            $t->string('supplier', 150)->nullable();
            $t->string('nomor_nota', 100)->nullable();
            $t->string('nota_path')->nullable();

            // Penyusutan (garis lurus)
            $t->unsignedSmallInteger('masa_manfaat_tahun')->default(4);
            $t->decimal('nilai_sisa', 15, 2)->default(0);

            // Kondisi & penempatan
            $t->string('kondisi', 20)->default('baik');
            $t->string('status', 20)->default('aktif');
            $t->string('departemen', 100)->nullable();
            $t->string('lokasi', 150)->nullable();
            $t->string('pengguna', 100)->nullable();
            $t->date('tanggal_penghapusan')->nullable();
            $t->text('catatan')->nullable();
            $t->timestamps();

            $t->index('status');
            $t->index('kondisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
