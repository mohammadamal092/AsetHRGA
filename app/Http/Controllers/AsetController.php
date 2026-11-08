<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AsetController extends Controller
{
    private function query(Request $r)
    {
        return Aset::with('kategori')
            ->when($r->filled('q'), function ($q) use ($r) {
                $s = '%' . $r->q . '%';
                $q->where(function ($w) use ($s) {
                    $w->where('nama', 'ilike', $s)
                      ->orWhere('kode_aset', 'ilike', $s)
                      ->orWhere('merk', 'ilike', $s)
                      ->orWhere('model', 'ilike', $s)
                      ->orWhere('nomor_seri', 'ilike', $s)
                      ->orWhere('pengguna', 'ilike', $s)
                      ->orWhere('lokasi', 'ilike', $s)
                      ->orWhere('departemen', 'ilike', $s);
                });
            })
            ->when($r->filled('kategori_id'), fn ($q) => $q->where('kategori_id', $r->kategori_id))
            ->when($r->filled('kondisi'), fn ($q) => $q->where('kondisi', $r->kondisi))
            ->when($r->filled('status'), fn ($q) => $q->where('status', $r->status))
            ->orderByDesc('created_at');
    }

    private function rules(): array
    {
        return [
            'nama'                => 'required|string|max:150',
            'kategori_id'         => 'required|exists:kategori_aset,id',
            'merk'                => 'nullable|string|max:100',
            'model'               => 'nullable|string|max:100',
            'nomor_seri'          => 'nullable|string|max:100',
            'tanggal_pembelian'   => 'required|date|before_or_equal:today',
            'harga_beli'          => 'required|numeric|min:0',
            'supplier'            => 'nullable|string|max:150',
            'nomor_nota'          => 'nullable|string|max:100',
            'nota'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'masa_manfaat_tahun'  => 'required|integer|min:1|max:50',
            'nilai_sisa'          => 'nullable|numeric|min:0',
            'kondisi'             => ['required', Rule::in(array_keys(Aset::KONDISI))],
            'status'              => ['required', Rule::in(array_keys(Aset::STATUS))],
            'departemen'          => 'nullable|string|max:100',
            'lokasi'              => 'nullable|string|max:150',
            'pengguna'            => 'nullable|string|max:100',
            'tanggal_penghapusan' => 'nullable|date|after_or_equal:tanggal_pembelian',
            'catatan'             => 'nullable|string|max:2000',
        ];
    }

    private function normalisasi(array $data): array
    {
        unset($data['nota']);
        $data['nilai_sisa'] = $data['nilai_sisa'] ?? 0;

        if ($data['status'] === 'dihapus') {
            $data['tanggal_penghapusan'] = $data['tanggal_penghapusan'] ?? now()->toDateString();
        } else {
            $data['tanggal_penghapusan'] = null;
        }
        return $data;
    }

    public function index(Request $request)
    {
        return view('aset.index', [
            'asets'    => $this->query($request)->paginate(15)->withQueryString(),
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('aset.create', [
            'aset'     => new Aset(),
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $payload = $this->normalisasi($data);

        if ($request->hasFile('nota')) {
            $payload['nota_path'] = $request->file('nota')->store('nota', 'local');
        }

        $aset = Aset::create($payload);
        $aset->riwayat()->create([
            'jenis'        => 'pembelian',
            'tanggal'      => $aset->tanggal_pembelian,
            'keterangan'   => 'Aset dicatat. Pembelian awal' . ($aset->supplier ? " dari {$aset->supplier}" : '')
                              . ($aset->nomor_nota ? ", nota {$aset->nomor_nota}" : '') . '.',
            'dicatat_oleh' => $request->user()->name,
        ]);

        return redirect()->route('aset.show', $aset)->with('sukses', "Aset {$aset->kode_aset} berhasil disimpan.");
    }

    public function show(Aset $aset)
    {
        $aset->load('kategori', 'riwayat');
        return view('aset.show', compact('aset'));
    }

    public function edit(Aset $aset)
    {
        return view('aset.edit', [
            'aset'     => $aset,
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Aset $aset)
    {
        $data = $request->validate($this->rules());
        $payload = $this->normalisasi($data);

        if ($request->hasFile('nota')) {
            if ($aset->nota_path) {
                Storage::disk('local')->delete($aset->nota_path);
            }
            $payload['nota_path'] = $request->file('nota')->store('nota', 'local');
        }

        $aset->update($payload);

        return redirect()->route('aset.show', $aset)->with('sukses', 'Data aset berhasil diperbarui.');
    }

    public function destroy(Aset $aset)
    {
        if ($aset->nota_path) {
            Storage::disk('local')->delete($aset->nota_path);
        }
        $aset->delete();

        return redirect()->route('aset.index')->with('sukses', 'Aset dihapus permanen.');
    }

    /** Tampilkan / unduh file nota (hanya untuk user yang login). */
    public function nota(Request $request, Aset $aset)
    {
        abort_unless($aset->nota_path && Storage::disk('local')->exists($aset->nota_path), 404);

        if ($request->boolean('unduh')) {
            $ext = pathinfo($aset->nota_path, PATHINFO_EXTENSION);
            return Storage::disk('local')->download($aset->nota_path, "nota-{$aset->kode_aset}.{$ext}");
        }
        return Storage::disk('local')->response($aset->nota_path);
    }

    /** Cetak kartu aset + bukti pembelian satu aset. */
    public function cetak(Aset $aset)
    {
        $aset->load('kategori', 'riwayat');
        return view('aset.cetak', compact('aset'));
    }

    /** Cetak daftar aset sesuai filter. */
    public function cetakDaftar(Request $request)
    {
        return view('aset.cetak-daftar', ['asets' => $this->query($request)->get()]);
    }

    public function export(Request $request)
    {
        $asets = $this->query($request)->get();

        return response()->streamDownload(function () use ($asets) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar terbaca benar di Excel
            fputcsv($out, [
                'Kode', 'Nama', 'Kategori', 'Merk', 'Model', 'No. Seri', 'Tgl Pembelian', 'Harga Beli',
                'Supplier', 'No. Nota', 'Umur', 'Masa Manfaat (thn)', 'Nilai Buku', 'Kondisi', 'Status',
                'Departemen', 'Lokasi', 'Pengguna',
            ], ';');
            foreach ($asets as $a) {
                fputcsv($out, [
                    $a->kode_aset, $a->nama, $a->kategori->nama, $a->merk, $a->model, $a->nomor_seri,
                    $a->tanggal_pembelian->format('d/m/Y'), $a->harga_beli, $a->supplier, $a->nomor_nota,
                    $a->umur, $a->masa_manfaat_tahun, round($a->nilai_buku), $a->kondisi_label, $a->status_label,
                    $a->departemen, $a->lokasi, $a->pengguna,
                ], ';');
            }
            fclose($out);
        }, 'daftar-aset-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
