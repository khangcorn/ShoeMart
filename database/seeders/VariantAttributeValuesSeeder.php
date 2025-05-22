<?php

namespace Database\Seeders;

use App\Models\VariantAttributeValues;
use Illuminate\Database\Seeder;

class VariantAttributeValuesSeeder extends Seeder
{
    public function run(): void
    {
        VariantAttributeValues::factory()->count(10)->create();
    }
}
