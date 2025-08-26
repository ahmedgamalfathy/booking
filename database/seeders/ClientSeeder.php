<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('clients')->insert([
                'name' => 'Client ' . $i,
                'type' => 'type' . $i ,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
