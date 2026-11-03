<div class="filter-box mb-3">
    <h4>Filter</h4>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label>Kode</label>
                <input type="text" class="form-control" id="filter-kode">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" id="filter-nama">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Harga Min</label>
                <input type="number" class="form-control" id="filter-harga-min">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Harga Max</label>
                <input type="number" class="form-control" id="filter-harga-max">
            </div>
        </div>
    </div>
    <button class="btn btn-primary mt-2 btn-get-data">Filter</button>
    <button class="btn btn-secondary mt-2 btn-reset-filter">Reset</button>
    <span id="loading-filter" style="display: none;" class="ms-2">Loading...</span>
</div>
