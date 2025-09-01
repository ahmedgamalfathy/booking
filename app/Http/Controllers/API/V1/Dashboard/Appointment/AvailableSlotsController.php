<?php

namespace App\Http\Controllers\API\V1\Dashboard\Appointment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service\Service;

class AvailableSlotsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
     $date = Carbon::parse($request->query('date'));
    $day  = $date->dayOfWeek;
    $sid  = $request->query('serviceId');

    $services = Service::with([
        'times' => fn($q) => $q->where('day_of_week', $day),
        'exceptions' => fn($q) => $q->whereDate('date', $date->toDateString()),
        'appointments' => fn($q) => $q->whereDate('date', $date->toDateString()),
    ])->when($sid, fn($q) => $q->where('id', $sid))->get();

    $data = $services->map(function ($service) {
        $slots = collect();
        foreach ($service->times as $time) {
            $start = Carbon::parse($time->start_time);
            $end   = Carbon::parse($time->end_time);

            while ($start->lt($end)) {
                $slotEnd = (clone $start)->addMinutes($time->session_time);
                if ($slotEnd->gt($end)) break;

                $blockedByException = $service->exceptions
                    ->where('is_available', false)
                    ->some(fn($ex) => $start->lt(Carbon::parse($ex->end_time)) && Carbon::parse($ex->start_time)->lt($slotEnd));

                $booked = $service->appointments
                    ->some(fn($app) => $start->lt(Carbon::parse($app->end_at)) && Carbon::parse($app->start_at)->lt($slotEnd));

                $slots->push([
                    'start' => $start->format('H:i'),
                    'end'   => $slotEnd->format('H:i'),
                    'available' => !($blockedByException || $booked),
                ]);

                $start->addMinutes($time->session_time);
            }
        }

        // مبدئياً تجاهلنا exceptions المتاحة (is_available = true) لو خارج الشيفت
        // تقدر تضيف loop تاني هنا لتوليد slots إضافية منها

        return [
            'serviceId'     => $service->id,
            // 'name'   => $service->name,
            // 'price'  => $service->price,
            // 'color'  => $service->color,
            // 'status' => $service->status,
            // 'type'   => $service->type,
            'date'   => request('date'),
            'slots'  => $slots->values(),
        ];
    });

    return response()->json(['services' => $data]);
}
}
