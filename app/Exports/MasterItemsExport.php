<?php

namespace App\Exports;

use App\Models\MasterItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterItemsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return MasterItem::with('kategoris')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Kategori',
            'Nama Items',
            'Nama Supplier',
            'Harga',
            'Laba',
            'Harga Jual'
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $kategoriNames = $item->kategoris->pluck('nama')->implode(', ');
        $hargaJual = $item->harga_beli + (($item->laba / 100) * $item->harga_beli);

        return [
            $no,
            $kategoriNames ?: '-',
            $item->nama,
            $item->supplier,
            $item->harga_beli,
            $item->laba . '%',
            $hargaJual
        ];
    }
}

