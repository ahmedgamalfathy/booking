<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting\Param\Param;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ParamSeeder extends Seeder
{


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $params = [
            ['type' => 'مزعج', 'color' => '#FF5733'],
            ['type' => 'خاص', 'color' => '#33FF57'],
            ['type' => 'مؤقت', 'color' => '#3357FF'],
            ['type' => 'دائم', 'color' => '#F1C40F'],
            ['type' => 'مهم', 'color' => '#8E44AD'],
            ['type' => 'عادي', 'color' => '#2ECC71'],
        ];
        foreach ($params as $param) {
            Param::create($param);
        }
    }
}
