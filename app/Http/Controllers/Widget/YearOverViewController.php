<?php

namespace App\Http\Controllers\Widget;

use Carbon\Carbon;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\AppointmentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Appointment\Appointment;

class YearOverViewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
   $monthlyReservations = Appointment::select(
            DB::raw('COUNT(*) as totalReservations, DATE_FORMAT(date, "%m-%Y") as month')
        )
        // ->whereBetween('date', [Carbon::today()->subMonths(6)->startOfMonth(), Carbon::today()->endOfDay()])
        ->where('date', '>=', Carbon::now()->subMonths(5)->startOfMonth()) // بداية آخر سبعة أشهر
        ->where('date', '<=', Carbon::now()->endOfMonth())
        ->where('status',AppointmentStatusEnum::APPROVED->value)
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        return ApiResponse::success(['yearOverview'=>$monthlyReservations]);
    }
}
