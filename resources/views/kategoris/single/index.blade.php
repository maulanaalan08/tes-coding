@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between mb-2">
                <a href="{{ route('kategoris.index') }}" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
                <a href="{{ route('kategoris.export-pdf', $data->id) }}" class="btn btn-danger">
                    Download PDF
                </a>
            </div>
            <div class="card mb-4">
                <div class="card-header">Detail Kategori</div>

                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">Kode Kategori</th>
                            <td style="width: 20px;">:</td>
                            <td>{{$data->kode}}</td>
                        </tr>
                        <tr>
                            <th>Nama Kategori</th>
                            <td>:</td>
                            <td>{{$data->nama}}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Item</th>
                            <td>:</td>
                            <td>{{$data->masterItems ? $data->masterItems->count() : 0}} Item</td>
                        </tr>
                    </table>
                    <div class="mt-2">
                        <a class="btn btn-info text-white" href="{{ route('kategoris.edit', $data->id) }}">Edit</a>
                        <form action="{{ route('kategoris.destroy', $data->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Daftar Item dalam Kategori Ini</div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Jenis</th>
                                <th>Harga Beli</th>
                                <th>Laba</th>
                                <th>Harga Jual</th>
                                <th>Supplier</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data->masterItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->jenis }}</td>
                                <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                <td>{{ $item->laba }}%</td>
                                <td>Rp {{ number_format(round($item->harga_beli + ($item->harga_beli * $item->laba / 100)), 0, ',', '.') }}</td>
                                <td>{{ $item->supplier }}</td>
                                <td>
                                    <a href="{{ route('master-items.show', $item->kode) }}" class="btn btn-primary btn-sm">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Belum ada item yang terhubung dengan kategori ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection
