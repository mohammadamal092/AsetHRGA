<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return view('kategori.index', [
            'kategori' => Kategori::withCount('aset')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama' => 'required|string|max:100|unique:kategori_aset,nama']);
        Kategori::create($data);
        return back()->with('sukses', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_aset,nama,' . $kategori->id,
        ]);
        $kategori->update($data);
        return back()->with('sukses', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->aset()->exists()) {
            return back()->withErrors(['nama' => 'Kategori masih dipakai oleh aset, tidak bisa dihapus.']);
        }
        $kategori->delete();
        return back()->with('sukses', 'Kategori dihapus.');
    }
}
