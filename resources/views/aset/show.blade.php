@extends('layouts.app')
@section('title', $aset->kode_aset)
@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $ext = $aset->nota_path ? strtolower(pathinfo($aset->nota_path, PATHINFO_EXTENSION)) : null;
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h4 class="mb-0">{{ $aset->nama }}</h4>
        <div class="text-muted">{{ $aset->kode_aset }} · {{ $aset->kategori->nama }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('aset.cetak', $aset) }}" target="_blank" class="btn btn-outline-dark">Cetak Kartu & Nota</a>
        <a href="{{ route('aset.edit', $aset) }}" class="btn btn-outline-secondary">Edit</a>
        <form method="POST" action="{{ route('aset.destroy', $aset) }}" onsubmit="return confirm('Hapus permanen aset ini beserta riwayat dan notanya? Jika hanya tidak terpakai lagi, ubah status menjadi Dihapus agar datanya tetap tersimpan.')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">Hapus</button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-body">
            <div class="row g-3 text-center">
                <div class="col-md-4">
                    <div class="text-muted small">Umur Barang</div>
                    <div class="fs-5 fw-bold text-{{ $aset->umur_warna === 'warning' ? 'warning-emphasis' : $aset->umur_warna }}">{{ $aset->umur }}</div>
                    <div class="text-muted small">{{ number_format($aset->umur_hari, 0, ',', '.') }} hari
                        @if($aset->status === 'dihapus') · berhenti {{ optional($aset->tanggal_penghapusan)->format('d/m/Y') }} @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Nilai Buku Saat Ini</div>
                    <div class="fs-5 fw-bold">{{ $rp($aset->nilai_buku) }}</div>
                    <div class="text-muted small">dari {{ $rp($aset->harga_beli) }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Akhir Masa Manfaat</div>
                    <div class="fs-5 fw-bold">{{ $aset->tanggal_akhir_manfaat->format('d/m/Y') }}</div>
                    <div class="text-muted small">
                        @if($aset->sisa_bulan > 0) sisa {{ intdiv($aset->sisa_bulan, 12) }} thn {{ $aset->sisa_bulan % 12 }} bln
                        @else <span class="text-danger">sudah terlewati</span> @endif
                    </div>
                </div>
            </div>
            <div class="progress mt-3" style="height: 10px">
                <div class="progress-bar bg-{{ $aset->umur_warna }}" style="width: {{ $aset->persen_umur }}%"></div>
            </div>
            <div class="small text-muted mt-1">{{ $aset->persen_umur }}% dari masa manfaat {{ $aset->masa_manfaat_tahun }} tahun terpakai</div>
        </div></div>

        <div class="card mb-3"><div class="card-body">
            <h6>Detail Barang</h6>
            <table class="table table-sm mb-0">
                <tr><th style="width:35%">Merk / Model</th><td>{{ trim($aset->merk.' '.$aset->model) ?: '-' }}</td></tr>
                <tr><th>Nomor Seri</th><td>{{ $aset->nomor_seri ?: '-' }}</td></tr>
                <tr><th>Kondisi</th><td><span class="badge text-bg-{{ $aset->kondisi_warna }}">{{ $aset->kondisi_label }}</span></td></tr>
                <tr><th>Status</th><td><span class="badge text-bg-{{ $aset->status_warna }}">{{ $aset->status_label }}</span></td></tr>
                <tr><th>Departemen</th><td>{{ $aset->departemen ?: '-' }}</td></tr>
                <tr><th>Lokasi</th><td>{{ $aset->lokasi ?: '-' }}</td></tr>
                <tr><th>Pengguna</th><td>{{ $aset->pengguna ?: '-' }}</td></tr>
                <tr><th>Catatan</th><td>{!! nl2br(e($aset->catatan ?: '-')) !!}</td></tr>
            </table>
        </div></div>

        <div class="card"><div class="card-body">
            <h6>Riwayat Aset</h6>
            <form method="POST" action="{{ route('aset.riwayat.store', $aset) }}" class="border rounded p-3 mb-3 bg-light">
                @csrf
                <div class="row g-2">
                    <div class="col-md-5">
                        <select name="jenis" class="form-select form-select-sm" required>
                            @foreach(\App\Models\RiwayatAset::JENIS as $val => $l)
                                @continue($val === 'pembelian')
                                <option value="{{ $val }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3"><input type="date" name="tanggal" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" class="form-control form-control-sm" required></div>
                    <div class="col-md-12"><input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Keterangan, mis. dipindah ke ruang meeting / ganti SSD" required></div>
                    <div class="col-12 small text-muted mt-2">Opsional, ubah data aset sekaligus:</div>
                    <div class="col-md-3"><input type="text" name="pengguna_baru" class="form-control form-control-sm" placeholder="Pengguna baru"></div>
                    <div class="col-md-3"><input type="text" name="lokasi_baru" class="form-control form-control-sm" placeholder="Lokasi baru"></div>
                    <div class="col-md-3">
                        <select name="kondisi_baru" class="form-select form-select-sm"><option value="">Kondisi (tetap)</option>
                            @foreach(\App\Models\Aset::KONDISI as $val => $l)<option value="{{ $val }}">{{ $l }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status_baru" class="form-select form-select-sm"><option value="">Status (tetap)</option>
                            @foreach(\App\Models\Aset::STATUS as $val => $l)<option value="{{ $val }}">{{ $l }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12"><button class="btn btn-sm btn-dark">Tambah Riwayat</button></div>
                </div>
            </form>

            <ul class="list-group list-group-flush">
                @foreach($aset->riwayat as $r)
                    <li class="list-group-item px-0">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $r->jenis_label }}</strong>
                            <span class="text-muted small">{{ $r->tanggal->format('d/m/Y') }}</span>
                        </div>
                        <div>{{ $r->keterangan }}</div>
                        @if($r->dicatat_oleh)<div class="text-muted small">dicatat oleh {{ $r->dicatat_oleh }}</div>@endif
                    </li>
                @endforeach
            </ul>
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-body">
            <h6>Pembelian Awal</h6>
            <table class="table table-sm mb-0">
                <tr><th>Tanggal</th><td>{{ $aset->tanggal_pembelian->format('d/m/Y') }}</td></tr>
                <tr><th>Harga</th><td>{{ $rp($aset->harga_beli) }}</td></tr>
                <tr><th>Supplier</th><td>{{ $aset->supplier ?: '-' }}</td></tr>
                <tr><th>No. Nota</th><td>{{ $aset->nomor_nota ?: '-' }}</td></tr>
                <tr><th>Nilai Sisa</th><td>{{ $rp($aset->nilai_sisa) }}</td></tr>
            </table>
        </div></div>

        <div class="card"><div class="card-body">
            <h6>File Nota</h6>
            @if($aset->nota_path)
                @if($ext === 'pdf')
                    <iframe src="{{ route('aset.nota', $aset) }}" class="w-100 border rounded" style="height:320px"></iframe>
                @else
                    <a href="{{ route('aset.nota', $aset) }}" target="_blank"><img src="{{ route('aset.nota', $aset) }}" class="img-fluid border rounded" alt="Nota"></a>
                @endif
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('aset.nota', $aset) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Buka</a>
                    <a href="{{ route('aset.nota', ['aset' => $aset, 'unduh' => 1]) }}" class="btn btn-sm btn-outline-secondary">Unduh</a>
                </div>
            @else
                <div class="text-muted">Belum ada nota. <a href="{{ route('aset.edit', $aset) }}">Upload sekarang</a>.</div>
            @endif
        </div></div>
    </div>
</div>
@endsection
