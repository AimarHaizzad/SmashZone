<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'old_price',
        'quantity',
        'image',
        'category',
        'brand',
    ];
}
