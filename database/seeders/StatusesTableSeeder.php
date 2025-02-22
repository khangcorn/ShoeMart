<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('statuses')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('statuses')->insert([
            ['name' => 'Active', 'description' => 'Active status', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inactive', 'description' => 'Inactive status', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
