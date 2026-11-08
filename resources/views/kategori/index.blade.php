@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<h4 class="mb-3">Kategori Aset</h4>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h6>Tambah Kategori</h6>
            <form method="POST" action="{{ route('kategori.store') }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="nama" class="form-control" placeholder="mis. Proyektor" required>
                <button class="btn btn-dark">Tambah</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr><th>Nama</th><th>Jumlah Aset</th><th></th></tr></thead>
                <tbody>
                @foreach($kategori as $k)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('kategori.update', $k) }}" class="d-flex gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="nama" value="{{ $k->nama }}" class="form-control form-control-sm" required>
                                <button class="btn btn-sm btn-outline-secondary">Simpan</button>
                            </form>
                        </td>
                        <td>{{ $k->aset_count }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('kategori.destroy', $k) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" @disabled($k->aset_count > 0)>Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div></div>
    </div>
</div>
@endsection
