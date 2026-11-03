<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [1, 7] }
            ]
        });
        getData();
    });

    $('.btn-get-data').click(function() {
        getData();
    });

    $('.btn-reset-filter').click(function() {
        $('#filter-kode').val('');
        $('#filter-nama').val('');
        $('#filter-harga-min').val('');
        $('#filter-harga-max').val('');
        getData();
    });

    function getData() {
        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        var filter_kode = $('#filter-kode').val();
        var filter_nama = $('#filter-nama').val();
        var filter_harga_min = $('#filter-harga-min').val();
        var filter_harga_max = $('#filter-harga-max').val();
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{route("master-items.search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: filter_kode,
                nama: filter_nama,
                hargamin: filter_harga_min,
                hargamax: filter_harga_max
            },
            success: function(results) {
                var data = results.data;

                $.each(data, function(index, item) {
                    var harga_jual = item.harga_beli + (item.harga_beli * item.laba / 100);
                    harga_jual = Math.round(harga_jual);
                    var kode = item.kode;

                    var foto_url = item.foto_url ? item.foto_url : (item.foto ? '{{ asset("uploads") }}/' + item.foto : '');
                    var foto_html = foto_url 
                        ? `<img src="${foto_url}" alt="${item.nama}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">` 
                        : `<span class="text-muted">-</span>`;

                    var htmlAction = `
                        <a href="{{url('master-items/view/')}}/${kode}" class="btn btn-primary btn-sm">View</a>
                        <a href="{{url('master-items')}}/${item.id}/edit" class="btn btn-info btn-sm text-white">Edit</a>
                        <form action="{{url('master-items')}}/${item.id}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?');">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    `;

                    var rowData = [
                        item.kode,
                        foto_html,
                        item.nama,
                        item.jenis,
                        'Rp ' + Number(item.harga_beli).toLocaleString('id-ID'),
                        'Rp ' + Number(harga_jual).toLocaleString('id-ID'),
                        item.supplier,
                        htmlAction
                    ];

                    dataTableObj.row.add(rowData).draw(true);
                });
                $('#loading-filter').hide();
            },
            error: function(xhr, textStatus, errorThrown) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    return;
                }
                alert('Terjadi kesalahan server, tidak dapat mengambil data');
                $('#loading-filter').hide();
            }
        });
    }
</script>
