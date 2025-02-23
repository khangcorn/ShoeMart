<?php

namespace Database\Seeders;

use App\Models\VariantAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantAttributeSeeder extends Seeder {
    public function run(): void {
        VariantAttribute::factory(20)->create();
    }
}

