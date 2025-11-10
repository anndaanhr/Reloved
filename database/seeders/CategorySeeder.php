<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'slug' => 'electronic',
                'description' => 'Barang elektronik preloved',
                'children' => [
                    ['name' => 'Smartphone', 'slug' => 'smartphone'],
                    ['name' => 'Laptop', 'slug' => 'laptop'],
                    ['name' => 'Tablet', 'slug' => 'tablet'],
                    ['name' => 'Kamera', 'slug' => 'kamera'],
                    ['name' => 'Audio', 'slug' => 'audio'],
                    ['name' => 'TV & Monitor', 'slug' => 'tv-monitor'],
                    ['name' => 'Gaming', 'slug' => 'gaming'],
                    ['name' => 'Aksesoris Elektronik', 'slug' => 'aksesoris-elektronik'],
                ],
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Pakaian dan aksesoris fashion preloved',
                'children' => [
                    ['name' => 'Pakaian Pria', 'slug' => 'pakaian-pria'],
                    ['name' => 'Pakaian Wanita', 'slug' => 'pakaian-wanita'],
                    ['name' => 'Sepatu', 'slug' => 'sepatu'],
                    ['name' => 'Tas', 'slug' => 'tas'],
                    ['name' => 'Aksesoris Fashion', 'slug' => 'aksesoris-fashion'],
                    ['name' => 'Jam Tangan', 'slug' => 'jam-tangan'],
                    ['name' => 'Perhiasan', 'slug' => 'perhiasan'],
                ],
            ],
            [
                'name' => 'Perawatan',
                'slug' => 'perawatan',
                'description' => 'Produk perawatan tubuh dan kecantikan',
                'children' => [
                    ['name' => 'Skincare', 'slug' => 'skincare'],
                    ['name' => 'Makeup', 'slug' => 'makeup'],
                    ['name' => 'Perawatan Rambut', 'slug' => 'perawatan-rambut'],
                    ['name' => 'Perawatan Tubuh', 'slug' => 'perawatan-tubuh'],
                    ['name' => 'Parfum', 'slug' => 'parfum'],
                ],
            ],
            [
                'name' => 'Anak-anak',
                'slug' => 'anakanak',
                'description' => 'Barang untuk anak-anak',
                'children' => [
                    ['name' => 'Pakaian Anak', 'slug' => 'pakaian-anak'],
                    ['name' => 'Mainan', 'slug' => 'mainan'],
                    ['name' => 'Perlengkapan Bayi', 'slug' => 'perlengkapan-bayi'],
                    ['name' => 'Sepatu Anak', 'slug' => 'sepatu-anak'],
                    ['name' => 'Buku Anak', 'slug' => 'buku-anak'],
                ],
            ],
            [
                'name' => 'Rumah',
                'slug' => 'rumah',
                'description' => 'Perabotan dan dekorasi rumah',
                'children' => [
                    ['name' => 'Furniture', 'slug' => 'furniture'],
                    ['name' => 'Dekorasi', 'slug' => 'dekorasi'],
                    ['name' => 'Peralatan Dapur', 'slug' => 'peralatan-dapur'],
                    ['name' => 'Tempat Tidur', 'slug' => 'tempat-tidur'],
                    ['name' => 'Elektronik Rumah', 'slug' => 'elektronik-rumah'],
                ],
            ],
            [
                'name' => 'Hobi',
                'slug' => 'hobi',
                'description' => 'Barang untuk hobi dan koleksi',
                'children' => [
                    ['name' => 'Musik', 'slug' => 'musik'],
                    ['name' => 'Olahraga', 'slug' => 'olahraga'],
                    ['name' => 'Koleksi', 'slug' => 'koleksi'],
                    ['name' => 'Alat Hobi', 'slug' => 'alat-hobi'],
                ],
            ],
            [
                'name' => 'Kendaraan',
                'slug' => 'kendaraan',
                'description' => 'Kendaraan dan aksesorisnya',
                'children' => [
                    ['name' => 'Motor', 'slug' => 'motor'],
                    ['name' => 'Mobil', 'slug' => 'mobil'],
                    ['name' => 'Sepeda', 'slug' => 'sepeda'],
                    ['name' => 'Aksesoris Kendaraan', 'slug' => 'aksesoris-kendaraan'],
                ],
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Perlengkapan olahraga',
                'children' => [
                    ['name' => 'Sepatu Olahraga', 'slug' => 'sepatu-olahraga'],
                    ['name' => 'Pakaian Olahraga', 'slug' => 'pakaian-olahraga'],
                    ['name' => 'Alat Olahraga', 'slug' => 'alat-olahraga'],
                    ['name' => 'Aksesoris Olahraga', 'slug' => 'aksesoris-olahraga'],
                ],
            ],
            [
                'name' => 'Buku',
                'slug' => 'buku',
                'description' => 'Buku dan literatur',
                'children' => [
                    ['name' => 'Buku Fiksi', 'slug' => 'buku-fiksi'],
                    ['name' => 'Buku Non-Fiksi', 'slug' => 'buku-non-fiksi'],
                    ['name' => 'Buku Pelajaran', 'slug' => 'buku-pelajaran'],
                    ['name' => 'Komik', 'slug' => 'komik'],
                ],
            ],
            [
                'name' => 'Makanan & Minuman',
                'slug' => 'makananminuman',
                'description' => 'Makanan dan minuman',
                'children' => [
                    ['name' => 'Makanan', 'slug' => 'makanan'],
                    ['name' => 'Minuman', 'slug' => 'minuman'],
                    ['name' => 'Snack', 'slug' => 'snack'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            // Check if category already exists
            $category = Category::where('slug', $categoryData['slug'])->first();
            if (!$category) {
                $category = Category::create($categoryData);
            }

            foreach ($children as $childData) {
                // Check if child category already exists
                $child = Category::where('slug', $childData['slug'])
                    ->where('parent_id', $category->id)
                    ->first();
                
                if (!$child) {
                    Category::create([
                        'name' => $childData['name'],
                        'slug' => $childData['slug'],
                        'parent_id' => $category->id,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}

