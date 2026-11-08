@extends('layouts.app')
@section('title', 'Tambah Aset')
@section('content')
<h4 class="mb-3">Tambah Aset</h4>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('aset.store') }}" enctype="multipart/form-data">
        @csrf
        @include('aset._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-dark">Simpan</button>
            <a href="{{ route('aset.index') }}" class="btn btn-light">Batal</a>
        </div>
    </form>
</div></div>
@endsection
