<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Aset</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; margin: 20px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 2px; } .sub { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #f1f1f1; }
        .bar { margin-bottom: 12px; } @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
<div class="bar no-print"><button onclick="window.print()">Cetak</button></div>
<h1>DAFTAR ASET KANTOR</h1>
<div class="sub">Dicetak {{ now()->format('d/m/Y H:i') }} · {{ $asets->count() }} aset · Total pembelian Rp {{ number_format($asets->sum('harga_beli'), 0, ',', '.') }}</div>
<table>
    <thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>Kategori</th><th>Merk / Model</th><th>No. Seri</th><th>Tgl Beli</th><th>Harga Beli</th><th>Umur</th><th>Kondisi</th><th>Status</th><th>Pengguna / Lokasi</th></tr></thead>
    <tbody>
    @foreach($asets as $i => $a)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $a->kode_aset }}</td><td>{{ $a->nama }}</td><td>{{ $a->kategori->nama }}</td>
            <td>{{ trim($a->merk.' '.$a->model) }}</td><td>{{ $a->nomor_seri }}</td>
            <td>{{ $a->tanggal_pembelian->format('d/m/Y') }}</td><td>{{ number_format($a->harga_beli, 0, ',', '.') }}</td>
            <td>{{ $a->umur }}</td><td>{{ $a->kondisi_label }}</td><td>{{ $a->status_label }}</td>
            <td>{{ $a->pengguna }}{{ $a->lokasi ? ' / '.$a->lokasi : '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
