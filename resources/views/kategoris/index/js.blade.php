<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#table-kategori').DataTable({
            searching: false,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [4] }
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
        getData();
    });

    function getData() {
        $('#loading-filter').show();
        var dataTableObj = $('#table-kategori').DataTable();
        var filter_kode = $('#filter-kode').val();
        var filter_nama = $('#filter-nama').val();
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{route("kategoris.search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: filter_kode,
                nama: filter_nama
            },
            success: function(results) {
                var data = results.data;

                $.each(data, function(index, item) {
                    var kode = item.kode;
                    var dateFormatted = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID') : '-';
                    var itemCount = item.master_items_count !== undefined ? item.master_items_count : 0;

                    var htmlAction = `
                        <a href="{{url('kategoris/view/')}}/${kode}" class="btn btn-primary btn-sm">View</a>
                        <a href="{{url('kategoris')}}/${item.id}/edit" class="btn btn-info btn-sm text-white">Edit</a>
                        <form action="{{url('kategoris')}}/${item.id}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    `;

                    var rowData = [
                        item.kode,
                        item.nama,
                        `<span class="badge bg-primary">${itemCount} item</span>`,
                        dateFormatted,
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
