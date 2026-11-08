@php
    $v = fn ($k, $d = null) => old($k, $aset->$k ?? $d);
    $tgl = fn ($k) => old($k, optional($aset->$k)->format('Y-m-d'));
@endphp

<div class="row g-3">
    <div class="col-12"><h6 class="text-muted text-uppercase small mb-0">Identitas Barang</h6></div>
    <div class="col-md-6">
        <label class="form-label">Nama Barang *</label>
        <input type="text" name="nama" value="{{ $v('nama') }}" class="form-control" placeholder="mis. PC Admin HRGA" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Kategori *</label>
        <select name="kategori_id" class="form-select" required>
            <option value="">— pilih —</option>
            @foreach($kategori as $k)<option value="{{ $k->id }}" @selected($v('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-4"><label class="form-label">Merk</label><input type="text" name="merk" value="{{ $v('merk') }}" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Model / Tipe</label><input type="text" name="model" value="{{ $v('model') }}" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Nomor Seri</label><input type="text" name="nomor_seri" value="{{ $v('nomor_seri') }}" class="form-control"></div>

    <div class="col-12 mt-4"><h6 class="text-muted text-uppercase small mb-0">Pembelian Awal & Nota</h6></div>
    <div class="col-md-4"><label class="form-label">Tanggal Pembelian *</label><input type="date" name="tanggal_pembelian" value="{{ $tgl('tanggal_pembelian') }}" max="{{ now()->toDateString() }}" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label">Harga Beli (Rp) *</label><input type="number" name="harga_beli" value="{{ $v('harga_beli', 0) }}" min="0" step="1" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label">Supplier / Toko</label><input type="text" name="supplier" value="{{ $v('supplier') }}" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Nomor Nota / Invoice</label><input type="text" name="nomor_nota" value="{{ $v('nomor_nota') }}" class="form-control"></div>
    <div class="col-md-8">
        <label class="form-label">Upload Nota (JPG/PNG/PDF, maks 4 MB)</label>
        <input type="file" name="nota" accept=".jpg,.jpeg,.png,.pdf" class="form-control">
        @if($aset->nota_path)
            <div class="form-text">Nota tersimpan: <a href="{{ route('aset.nota', $aset) }}" target="_blank">lihat</a>. Upload file baru untuk menggantinya.</div>
        @endif
    </div>

    <div class="col-12 mt-4"><h6 class="text-muted text-uppercase small mb-0">Umur Pakai & Penyusutan</h6></div>
    <div class="col-md-4">
        <label class="form-label">Masa Manfaat (tahun) *</label>
        <input type="number" name="masa_manfaat_tahun" value="{{ $v('masa_manfaat_tahun', 4) }}" min="1" max="50" class="form-control" required>
        <div class="form-text">Perkiraan lama barang layak pakai.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Nilai Sisa / Residu (Rp)</label>
        <input type="number" name="nilai_sisa" value="{{ $v('nilai_sisa', 0) }}" min="0" step="1" class="form-control">
    </div>

    <div class="col-12 mt-4"><h6 class="text-muted text-uppercase small mb-0">Kondisi & Penempatan</h6></div>
    <div class="col-md-4">
        <label class="form-label">Kondisi *</label>
        <select name="kondisi" class="form-select" required>
            @foreach(\App\Models\Aset::KONDISI as $val => $l)<option value="{{ $val }}" @selected($v('kondisi', 'baik') === $val)>{{ $l }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
            @foreach(\App\Models\Aset::STATUS as $val => $l)<option value="{{ $val }}" @selected($v('status', 'aktif') === $val)>{{ $l }}</option>@endforeach
        </select>
        <div class="form-text">Jika "Dihapus", hitungan umur berhenti di tanggal penghapusan.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tanggal Dihapus / Tidak Terpakai</label>
        <input type="date" name="tanggal_penghapusan" value="{{ $tgl('tanggal_penghapusan') }}" class="form-control">
    </div>
    <div class="col-md-4"><label class="form-label">Departemen</label><input type="text" name="departemen" value="{{ $v('departemen') }}" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Lokasi</label><input type="text" name="lokasi" value="{{ $v('lokasi') }}" class="form-control" placeholder="mis. Lantai 2 - Ruang HRGA"></div>
    <div class="col-md-4"><label class="form-label">Pengguna / Penanggung Jawab</label><input type="text" name="pengguna" value="{{ $v('pengguna') }}" class="form-control"></div>
    <div class="col-12"><label class="form-label">Catatan</label><textarea name="catatan" rows="3" class="form-control">{{ $v('catatan') }}</textarea></div>
</div>
