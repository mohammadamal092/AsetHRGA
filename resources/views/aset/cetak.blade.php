@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $ext = $aset->nota_path ? strtolower(pathinfo($aset->nota_path, PATHINFO_EXTENSION)) : null;
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Aset {{ $aset->kode_aset }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; margin: 0; padding: 24px; }
        .wrap { max-width: 780px; margin: 0 auto; }
        h1 { font-size: 18px; margin: 0; }
        .sub { color: #555; margin-bottom: 12px; }
        .head { display: flex; justify-content: space-between; border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 14px; }
        h2 { font-size: 13px; margin: 16px 0 6px; text-transform: uppercase; letter-spacing: .5px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #999; padding: 5px 8px; text-align: left; vertical-align: top; }
        th { width: 28%; background: #f1f1f1; font-weight: 600; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        .nota { margin-top: 8px; border: 1px solid #999; padding: 8px; text-align: center; }
        .nota img { max-width: 100%; max-height: 520px; }
        .ttd { display: flex; justify-content: space-around; margin-top: 40px; text-align: center; }
        .ttd div { width: 30%; } .ttd .line { margin-top: 60px; border-top: 1px solid #111; padding-top: 4px; }
        .bar { margin-bottom: 16px; }
        .bar button { padding: 8px 16px; cursor: pointer; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="bar no-print"><button onclick="window.print()">Cetak</button></div>

    <div class="head">
        <div><h1>KARTU ASET & BUKTI PEMBELIAN</h1><div class="sub">HRGA — Inventaris Aset Kantor</div></div>
        <div style="text-align:right"><strong style="font-size:16px">{{ $aset->kode_aset }}</strong><br>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="grid">
        <div>
            <h2>Identitas Barang</h2>
            <table>
                <tr><th>Nama</th><td>{{ $aset->nama }}</td></tr>
                <tr><th>Kategori</th><td>{{ $aset->kategori->nama }}</td></tr>
                <tr><th>Merk / Model</th><td>{{ trim($aset->merk.' '.$aset->model) ?: '-' }}</td></tr>
                <tr><th>No. Seri</th><td>{{ $aset->nomor_seri ?: '-' }}</td></tr>
                <tr><th>Kondisi</th><td>{{ $aset->kondisi_label }}</td></tr>
                <tr><th>Status</th><td>{{ $aset->status_label }}</td></tr>
            </table>
        </div>
        <div>
            <h2>Penempatan</h2>
            <table>
                <tr><th>Departemen</th><td>{{ $aset->departemen ?: '-' }}</td></tr>
                <tr><th>Lokasi</th><td>{{ $aset->lokasi ?: '-' }}</td></tr>
                <tr><th>Pengguna</th><td>{{ $aset->pengguna ?: '-' }}</td></tr>
            </table>
            <h2>Pembelian Awal</h2>
            <table>
                <tr><th>Tgl Pembelian</th><td>{{ $aset->tanggal_pembelian->format('d/m/Y') }}</td></tr>
                <tr><th>Harga Beli</th><td>{{ $rp($aset->harga_beli) }}</td></tr>
                <tr><th>Supplier</th><td>{{ $aset->supplier ?: '-' }}</td></tr>
                <tr><th>No. Nota</th><td>{{ $aset->nomor_nota ?: '-' }}</td></tr>
            </table>
        </div>
    </div>

    <h2>Umur & Nilai (per {{ $aset->tanggal_akhir_umur->format('d/m/Y') }})</h2>
    <table>
        <tr><th>Umur Barang</th><td>{{ $aset->umur }} ({{ number_format($aset->umur_hari, 0, ',', '.') }} hari)</td></tr>
        <tr><th>Masa Manfaat</th><td>{{ $aset->masa_manfaat_tahun }} tahun (berakhir {{ $aset->tanggal_akhir_manfaat->format('d/m/Y') }})</td></tr>
        <tr><th>Nilai Buku</th><td>{{ $rp($aset->nilai_buku) }}</td></tr>
        @if($aset->status === 'dihapus')<tr><th>Tgl Dihapus</th><td>{{ optional($aset->tanggal_penghapusan)->format('d/m/Y') }}</td></tr>@endif
    </table>

    <h2>Riwayat</h2>
    <table>
        @foreach($aset->riwayat as $r)
            <tr><td style="width:90px">{{ $r->tanggal->format('d/m/Y') }}</td><td style="width:200px">{{ $r->jenis_label }}</td><td>{{ $r->keterangan }}</td></tr>
        @endforeach
    </table>

    <h2>Bukti Nota</h2>
    <div class="nota">
        @if($aset->nota_path && $ext !== 'pdf')
            <img src="{{ route('aset.nota', $aset) }}" alt="Nota">
        @elseif($aset->nota_path)
            Nota berupa file PDF, buka dari halaman detail aset untuk melihat / mencetaknya.
        @else
            Belum ada file nota.
        @endif
    </div>

    <div class="ttd">
        <div>Dibuat oleh<div class="line">HRGA</div></div>
        <div>Diketahui<div class="line">Atasan</div></div>
        <div>Penerima / Pengguna<div class="line">{{ $aset->pengguna ?: '.................' }}</div></div>
    </div>
</div>
</body>
</html>
