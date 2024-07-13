<?php

namespace App\Imports;

use App\Models\Training;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TrainingImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Training([
            'nama_produk' => $row['nama_produk'],
            'ukuran' => $row['ukuran'],
            'stok_produk' => $row['stok_produk'],
            'output' => $row['output'],
        ]);
    }
}
