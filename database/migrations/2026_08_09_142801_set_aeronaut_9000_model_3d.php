<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Product::where('name', 'Li-Ning Aeronaut 9000')->update([
            'model_3d' => 'procedural:racket',
        ]);
    }

    public function down(): void
    {
        Product::where('name', 'Li-Ning Aeronaut 9000')->update([
            'model_3d' => null,
        ]);
    }
};
