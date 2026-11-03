@if($method == 'create')
<form action="{{ route('kategoris.store') }}" method="POST">
    @csrf
@else
<form action="{{ route('kategoris.update', $kategori->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group mb-3">
        <label>Kode Kategori</label>
        <input type="text" class="form-control" name="kode" required readonly value="{{$kategori->kode ?? ''}}">
    </div>
@endif

    <div class="form-group mb-3">
        <label>Nama Kategori</label>
        <input type="text" class="form-control" name="nama" required value="{{$kategori->nama ?? ''}}">
    </div>

    <button class="btn btn-primary mt-3">Submit</button>
</form>
