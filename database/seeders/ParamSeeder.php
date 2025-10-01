<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\Param\Param;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ParamSeeder extends Seeder
{


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('parameters')->insert([
            [
                'name'=>'clientType',
                'order'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'changeType',
                'order'=>2,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]
       ]);
        $params = [
            ['type' => 'مزعج', 'color' => '#FF5733','parameter_order'=>1],
            ['type' => 'خاص', 'color' => '#33FF57','parameter_order'=>1],
            ['type' => 'مؤقت', 'color' => '#3357FF','parameter_order'=>1],
            ['type' => 'دائم', 'color' => '#F1C40F','parameter_order'=>1],
            ['type' => 'مهم', 'color' => '#8E44AD','parameter_order'=>1],
            ['type' => 'عادي', 'color' => '#2ECC71','parameter_order'=>1],
            ['type' => 'eg-egypt', 'color' => '#2ECC71','parameter_order'=>2],
        ];
        foreach ($params as $param) {
            Param::create($param);
        }
    }
}
