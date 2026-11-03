<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:255|unique:kategoris,kode',
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi',
            'kode.unique' => 'Kode kategori sudah digunakan',
        ];
    }
}
