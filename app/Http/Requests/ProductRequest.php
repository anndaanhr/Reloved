<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'condition' => ['required', 'in:baru,lumayan_baru,bekas,rusak'],
            'stock' => ['required', 'integer', 'min:1'],
            'deal_method' => ['required', 'array'],
            'deal_method.*' => ['in:meetup,shipping'],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'max:2048'],
            'brand' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
        ];

        // For update, images are optional
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['images'] = ['sometimes', 'array', 'max:10'];
            $rules['images.*'] = ['image', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga minimal 0.',
            'condition.required' => 'Kondisi produk wajib dipilih.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.min' => 'Stok minimal 1.',
            'deal_method.required' => 'Metode transaksi wajib dipilih.',
            'images.required' => 'Minimal 1 gambar produk wajib diupload.',
            'images.max' => 'Maksimal 10 gambar produk.',
        ];
    }
}
