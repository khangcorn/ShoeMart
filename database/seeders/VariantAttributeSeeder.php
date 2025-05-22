<?php

namespace Database\Seeders;

use App\Models\VariantAttribute;
use Illuminate\Database\Seeder;

class VariantAttributeSeeder extends Seeder
{
    public function run(): void
    {
        VariantAttribute::factory()->count(10)->create();
    }
}
