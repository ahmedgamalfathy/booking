<?php

namespace Database\Seeders;


use App\Models\Client\Client;
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
        $clients=[
            ['name' => 'Client 1', 'param_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Client 2', 'param_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Client 3', 'param_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Client 4', 'param_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ];
        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
