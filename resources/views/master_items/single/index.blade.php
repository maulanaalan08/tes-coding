@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group mb-2">
                <a href="{{ route('master-items.index') }}" class="btn btn-secondary">Kembali ke Daftar Item</a>
            </div>
            <div class="card">
                <div class="card-header">Master Item</div>

                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">Kode</th>
                            <td style="width: 20px;">:</td>
                            <td>{{$data->kode}}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>:</td>
                            <td>{{$data->nama}}</td>
                        </tr>
                        <tr>
                            <th>Foto</th>
                            <td>:</td>
                            <td>
                                @if(!empty($data->foto))
                                    <img src="{{ $data->foto_url ?? asset('uploads/' . $data->foto) }}" alt="{{$data->nama}}" style="max-width: 250px; max-height: 250px; object-fit: contain; border-radius: 4px; border: 1px solid #ddd; padding: 2px;">
                                @else
                                    <span class="text-muted">Tidak ada foto</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>:</td>
                            <td>
                                @if(isset($data->kategoris) && $data->kategoris->count() > 0)
                                    @foreach($data->kategoris as $kat)
                                        <span class="badge bg-secondary">{{ $kat->nama }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Harga Beli</th>
                            <td>:</td>
                            <td>Rp {{ number_format($data->harga_beli, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Laba</th>
                            <td>:</td>
                            <td>{{$data->laba}}%</td>
                        </tr>
                        <tr>
                            <th>Harga Jual</th>
                            <td>:</td>
                            <td>Rp {{ number_format(round($data->harga_beli + ($data->harga_beli * $data->laba / 100)), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td>:</td>
                            <td>{{$data->supplier}}</td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td>:</td>
                            <td>{{$data->jenis}}</td>
                        </tr>
                    </table>
                    <div class="mt-3">
                        <a class="btn btn-info text-white" href="{{ route('master-items.edit', $data->id) }}">Edit</a>
                        <form action="{{ route('master-items.destroy', $data->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection
