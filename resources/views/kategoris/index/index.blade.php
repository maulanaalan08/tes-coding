@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between mb-2">
                <a href="{{ route('kategoris.create') }}" class="btn btn-secondary">+ Kategori Baru</a>
            </div>
            <div class="card">
                <div class="card-header">Daftar Kategori</div>

                <div class="card-body">
                    @include('kategoris.index.filter')
                    @include('kategoris.index.table')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@include('kategoris.index.js')
@endsection
