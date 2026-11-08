<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Kategori;

class DashboardController extends Controller
{
    public function index()
    {
        $aktif = Aset::with('kategori')->where('status', '!=', 'dihapus')->get();

        return view('dashboard', [
            'totalAktif'     => $aktif->count(),
            'totalDihapus'   => Aset::where('status', 'dihapus')->count(),
            'totalHarga'     => $aktif->sum('harga_beli'),
            'totalNilaiBuku' => $aktif->sum(fn ($a) => $a->nilai_buku),
            'perKondisi'     => $aktif->groupBy('kondisi')->map->count(),
            'perKategori'    => Kategori::withCount(['aset' => fn ($q) => $q->where('status', '!=', 'dihapus')])
                                    ->orderBy('nama')->get(),
            'perhatian'      => $aktif->filter(fn ($a) => in_array($a->status_umur, ['habis', 'segera']))
                                    ->sortBy('sisa_bulan')->values(),
            'terbaru'        => Aset::with('kategori')->latest()->take(5)->get(),
        ]);
    }
}
