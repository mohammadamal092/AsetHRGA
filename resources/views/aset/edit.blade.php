@extends('layouts.app')
@section('title', 'Edit Aset')
@section('content')
<h4 class="mb-3">Edit Aset <span class="text-muted">{{ $aset->kode_aset }}</span></h4>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('aset.update', $aset) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('aset._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-dark">Simpan Perubahan</button>
            <a href="{{ route('aset.show', $aset) }}" class="btn btn-light">Batal</a>
        </div>
    </form>
</div></div>
@endsection
