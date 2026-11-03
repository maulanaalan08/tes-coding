@if($method == 'create')
<form action="{{ route('master-items.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
@else
<form action="{{ route('master-items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-group mb-3">
        <label>Kode Barang</label>
        <input type="text" class="form-control" name="kode" required readonly value="{{$item->kode ?? ''}}">
    </div>
@endif

    <div class="form-group mb-3">
        <label>Nama</label>
        <input type="text" class="form-control" name="nama" required value="{{$item->nama ?? ''}}">
    </div>

    <div class="form-group mb-3">
        <label>Foto</label>
        <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
        @if($method == 'edit' && !empty($item->foto))
        <div class="mt-2">
            <small class="text-muted d-block mb-1">Foto saat ini:</small>
            <img src="{{ $item->foto_url ?? asset('uploads/' . $item->foto) }}" alt="{{ $item->nama ?? 'Foto' }}" style="max-height: 120px; border-radius: 4px; border: 1px solid #ddd; padding: 2px;">
        </div>
        @endif
    </div>

    <div class="form-group mb-3">
        <label for="kategoris">Kategori</label>
        <select name="kategoris[]" id="kategoris" class="form-control" multiple>
            @foreach($kategoris ?? [] as $kategori)
                <option value="{{ $kategori->id }}" 
                    {{ (isset($item->kategoris) && $item->kategoris->contains($kategori->id)) ? 'selected' : '' }}>
                    {{ $kategori->nama }} ({{ $kategori->kode }})
                </option>
            @endforeach
        </select>
        <small class="text-muted">Tekan Ctrl (atau Cmd) untuk memilih lebih dari satu kategori.</small>
    </div>

    <div class="form-group mb-3">
        <label>Harga Beli</label>
        <input type="number" class="form-control" name="harga_beli" required value="{{$item->harga_beli ?? ''}}">
    </div>

    <div class="form-group mb-3">
        <label>Laba (dalam persen)</label>
        <input type="number" class="form-control" name="laba" required value="{{$item->laba ?? ''}}">
    </div>

    @php $selected = $item->supplier ?? ''; @endphp
    <div class="form-group mb-3">
        <label>Supplier</label>
        <select class="form-control" required name="supplier">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            <option @if($selected == 'Tokopaedi') selected @endif>Tokopaedi</option>
            <option @if($selected == 'Bukulapuk') selected @endif>Bukulapuk</option>
            <option @if($selected == 'TokoBagas') selected @endif>TokoBagas</option>
            <option @if($selected == 'E Commurz') selected @endif>E Commurz</option>
            <option @if($selected == 'Blublu') selected @endif>Blublu</option>
        </select>
    </div>

    @php $selected = $item->jenis ?? ''; @endphp
    <div class="form-group mb-3">
        <label>Jenis</label>
        <select class="form-control" required name="jenis">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            <option @if($selected == 'Obat') selected @endif>Obat</option>
            <option @if($selected == 'Alkes') selected @endif>Alkes</option>
            <option @if($selected == 'Matkes') selected @endif>Matkes</option>
            <option @if($selected == 'Umum') selected @endif>Umum</option>
            <option @if($selected == 'ATK') selected @endif>ATK</option>
        </select>
    </div>

    <button class="btn btn-primary mt-3">Submit</button>

</form>
