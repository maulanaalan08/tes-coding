<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'laba' => 'required|numeric|min:0|max:100',
            'supplier' => 'required|string|max:255',
            'jenis' => 'required|in:Obat,Alkes,Matkes,Umum,ATK',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'kategoris' => 'nullable|array',
            'kategoris.*' => 'exists:kategoris,id',
        ];
    }

    public function messages()
    {
        return [
            'jenis.in' => 'Jenis harus salah satu dari: Obat, Alkes, Matkes, Umum, ATK',
            'kategoris.*.exists' => 'Kategori yang dipilih tidak valid',
        ];
    }
}
