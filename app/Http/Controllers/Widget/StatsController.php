<?php

namespace App\Http\Controllers\Widget;

use Carbon\Carbon;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Appointment\Appointment;

class StatsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $totalClient=Client::count();
        $totalAppointment=Appointment::count();
        $todayAppointment=Appointment::whereDate('date',Carbon::today())->count();
        $dailyIncome = DB::table('appointments')
            ->join('services', 'services.id', '=', 'appointments.service_id')
            ->where('services.price', '>', 0)
            ->sum('services.price');
       return  ApiResponse::success([
            "totalClient"=>$totalClient,
            "totalAppointment"=>$totalAppointment,
            "todayAppointment"=>$todayAppointment,
            "dailyIncom"=>$dailyIncome
        ]);
    }
}
