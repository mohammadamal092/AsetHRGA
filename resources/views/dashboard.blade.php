@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Dashboard</h4>
    <a href="{{ route('aset.create') }}" class="btn btn-dark">+ Tambah Aset</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted small">Aset Terpakai</div><div class="stat">{{ $totalAktif }}</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted small">Total Nilai Pembelian</div><div class="stat">Rp {{ number_format($totalHarga, 0, ',', '.') }}</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted small">Total Nilai Buku Saat Ini</div><div class="stat">Rp {{ number_format($totalNilaiBuku, 0, ',', '.') }}</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted small">Aset Dihapus / Tidak Terpakai</div><div class="stat">{{ $totalDihapus }}</div></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-body">
            <h6>Kondisi Aset</h6>
            @foreach(\App\Models\Aset::KONDISI as $k => $label)
                <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $label }}</span><strong>{{ $perKondisi[$k] ?? 0 }}</strong></div>
            @endforeach
        </div></div>
        <div class="card"><div class="card-body">
            <h6>Per Kategori</h6>
            @forelse($perKategori as $k)
                <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $k->nama }}</span><strong>{{ $k->aset_count }}</strong></div>
            @empty
                <div class="text-muted">Belum ada kategori.</div>
            @endforelse
        </div></div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-body">
            <h6>Perlu Perhatian <span class="text-muted fw-normal small">(masa manfaat habis / ≤ 6 bulan lagi)</span></h6>
            <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead><tr><th>Kode</th><th>Nama</th><th>Umur</th><th>Status Umur</th></tr></thead>
                <tbody>
                @forelse($perhatian as $a)
                    <tr>
                        <td><a href="{{ route('aset.show', $a) }}">{{ $a->kode_aset }}</a></td>
                        <td>{{ $a->nama }}</td>
                        <td>{{ $a->umur }}</td>
                        <td><span class="badge text-bg-{{ $a->umur_warna }}">{{ $a->status_umur === 'habis' ? 'Masa manfaat habis' : 'Sisa '.$a->sisa_bulan.' bln' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Tidak ada.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div></div>

        <div class="card"><div class="card-body">
            <h6>Aset Terbaru</h6>
            <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead><tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Tgl Beli</th></tr></thead>
                <tbody>
                @forelse($terbaru as $a)
                    <tr>
                        <td><a href="{{ route('aset.show', $a) }}">{{ $a->kode_aset }}</a></td>
                        <td>{{ $a->nama }}</td>
                        <td>{{ $a->kategori->nama }}</td>
                        <td>{{ $a->tanggal_pembelian->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Belum ada data aset.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div></div>
    </div>
</div>
@endsection
