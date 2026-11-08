@extends('layouts.app')
@section('title', 'Data Aset')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0">Data Aset</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('aset.cetak-daftar', request()->query()) }}" target="_blank" class="btn btn-outline-secondary">Cetak Daftar</a>
        <a href="{{ route('aset.export', request()->query()) }}" class="btn btn-outline-secondary">Export CSV</a>
        <a href="{{ route('aset.create') }}" class="btn btn-dark">+ Tambah Aset</a>
    </div>
</div>

<div class="card mb-3"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode, nama, merk, no. seri, pengguna, lokasi..."></div>
        <div class="col-md-2">
            <select name="kategori_id" class="form-select">
                <option value="">Semua kategori</option>
                @foreach($kategori as $k)<option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="kondisi" class="form-select">
                <option value="">Semua kondisi</option>
                @foreach(\App\Models\Aset::KONDISI as $v => $l)<option value="{{ $v }}" @selected(request('kondisi') === $v)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach(\App\Models\Aset::STATUS as $v => $l)<option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill">Cari</button>
            <a href="{{ route('aset.index') }}" class="btn btn-light">Reset</a>
        </div>
    </form>
</div></div>

<div class="card"><div class="card-body p-0">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Pengguna / Lokasi</th><th>Tgl Beli</th><th>Umur</th><th>Kondisi</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
    @forelse($asets as $a)
        <tr>
            <td><a href="{{ route('aset.show', $a) }}" class="fw-semibold text-decoration-none">{{ $a->kode_aset }}</a></td>
            <td>{{ $a->nama }}<div class="text-muted small">{{ trim($a->merk.' '.$a->model) }}</div></td>
            <td>{{ $a->kategori->nama }}</td>
            <td>{{ $a->pengguna ?: '-' }}<div class="text-muted small">{{ $a->lokasi }}</div></td>
            <td>{{ $a->tanggal_pembelian->format('d/m/Y') }}</td>
            <td><span class="text-{{ $a->umur_warna === 'warning' ? 'warning-emphasis' : ($a->umur_warna === 'secondary' ? 'secondary' : $a->umur_warna) }}">{{ $a->umur }}</span></td>
            <td><span class="badge text-bg-{{ $a->kondisi_warna }}">{{ $a->kondisi_label }}</span></td>
            <td><span class="badge text-bg-{{ $a->status_warna }}">{{ $a->status_label }}</span></td>
            <td class="text-end text-nowrap">
                <a href="{{ route('aset.show', $a) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                <a href="{{ route('aset.edit', $a) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
</div></div>
<div class="mt-3">{{ $asets->links() }}</div>
@endsection
