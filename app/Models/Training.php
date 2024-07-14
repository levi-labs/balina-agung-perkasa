<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'data_training';

    public function nama_produk(): Attribute
    {
        return new Attribute(
            get: fn ($value) => strtolower($value),
            set: fn ($value) => strtolower($value),
        );
    }
    public function stok_produk(): Attribute
    {
        return new Attribute(
            get: fn ($value) => strtolower($value),
            set: fn ($value) => strtolower($value),
        );
    }

    public function output(): Attribute
    {
        return new Attribute(
            get: fn ($value) => strtolower($value),
            set: fn ($value) => strtolower($value),
        );
    }
}
