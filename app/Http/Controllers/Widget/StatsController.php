<?php

namespace App\Http\Controllers\Widget;

use Carbon\Carbon;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use Illuminate\Support\Facades\DB;
use App\Enums\AppointmentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Appointment\Appointment;

class StatsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $total = Appointment::count();
        $accepted = Appointment::where('status', AppointmentStatusEnum::APPROVED->value)->count();
        $pending = Appointment::where('status', AppointmentStatusEnum::PENDING->value)->count();
        $rejected = Appointment::where('status', AppointmentStatusEnum::CANCELLED->value)->count();
        $acceptedPercentage = $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
        $rejectedPercentage = $total > 0 ? round(($rejected / $total) * 100, 2) : 0;
        $pendingPercentage = $total > 0 ? round(($pending / $total) * 100, 2) : 0;

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
            "dailyIncom"=>$dailyIncome,
            "acceptedPercentage"=>$acceptedPercentage,
            "rejectedPercentage"=>$rejectedPercentage,
            "pendingPercentage"=>$pendingPercentage 
        ]);
    }
}
