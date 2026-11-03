@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group mb-2">
                <a href="{{ route('kategoris.index') }}" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
            </div>
            <div class="card">
                @if($method == 'create')
                <div class="card-header">Buat Kategori Baru</div>
                @else
                <div class="card-header">Edit Kategori</div>
                @endif

                <div class="card-body">
                    @include('kategoris.form.form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection
