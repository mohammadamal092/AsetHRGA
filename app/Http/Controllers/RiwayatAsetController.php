<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\RiwayatAset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RiwayatAsetController extends Controller
{
    public function store(Request $request, Aset $aset)
    {
        $data = $request->validate([
            'jenis'         => ['required', Rule::in(array_keys(RiwayatAset::JENIS))],
            'tanggal'       => 'required|date|before_or_equal:today',
            'keterangan'    => 'required|string|max:1000',
            'pengguna_baru' => 'nullable|string|max:100',
            'lokasi_baru'   => 'nullable|string|max:150',
            'kondisi_baru'  => ['nullable', Rule::in(array_keys(Aset::KONDISI))],
            'status_baru'   => ['nullable', Rule::in(array_keys(Aset::STATUS))],
        ]);

        // Perubahan opsional pada data aset
        $ubah = array_filter([
            'pengguna' => $data['pengguna_baru'] ?? null,
            'lokasi'   => $data['lokasi_baru'] ?? null,
            'kondisi'  => $data['kondisi_baru'] ?? null,
            'status'   => $data['status_baru'] ?? null,
        ], fn ($v) => filled($v));

        if (isset($ubah['status'])) {
            $ubah['tanggal_penghapusan'] = $ubah['status'] === 'dihapus' ? $data['tanggal'] : null;
        }
        if ($ubah) {
            $aset->update($ubah);
        }

        $aset->riwayat()->create([
            'jenis'        => $data['jenis'],
            'tanggal'      => $data['tanggal'],
            'keterangan'   => $data['keterangan'],
            'dicatat_oleh' => $request->user()->name,
        ]);

        return back()->with('sukses', 'Riwayat aset ditambahkan.');
    }
}
