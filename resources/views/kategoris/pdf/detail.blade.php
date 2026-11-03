<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Kategori - {{ $kategori->nama }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h2 { margin-bottom: 4px; }
        .meta { margin-bottom: 20px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 10px; color: #666; text-align: right; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Detail Kategori Items</h2>
    <div class="meta">
        <strong>Kode Kategori:</strong> {{ $kategori->kode }}<br>
        <strong>Nama Kategori:</strong> {{ $kategori->nama }}<br>
        <strong>Total Items:</strong> {{ $items->count() }} item
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Kode Item</th>
                <th style="width: 25%;">Nama Item</th>
                <th style="width: 15%;">Jenis</th>
                <th style="width: 15%;" class="text-right">Harga Beli</th>
                <th style="width: 10%;" class="text-center">Laba</th>
                <th style="width: 15%;">Supplier</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->jenis }}</td>
                <td class="text-right">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->laba }}%</td>
                <td>{{ $item->supplier }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada item dalam kategori ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $tanggal_cetak }}
    </div>
</body>
</html>

