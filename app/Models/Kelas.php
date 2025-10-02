<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas'; // nama tabel di database

    public function getKelas()
    {
        return $this->all(); // ambil semua data kelas
}
}
