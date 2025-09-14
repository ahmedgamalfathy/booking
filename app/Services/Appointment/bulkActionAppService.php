<?php
namespace App\Services\Appointment;

use Carbon\Carbon;
use App\Models\Time\Time;
use App\Helpers\ApiResponse;
use App\Models\Exception\Exception;
use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment\Appointment;

class BulkActionAppService{
    /**
     * جلب الأيام المتاحة وغير المتاحة لخدمة معينة
     */
    public function getMonthlyAvailability($serviceId, $monthYear = null)
    {
    if ($monthYear) {
        [$year,$month] = explode('-', $monthYear); // "08-2025" → [08, 2025]
    } else {
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;
    }
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $result = [];
        $timesArr=[];
        $exceptionsArr=[];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dayOfWeek = $date->dayOfWeek;

            $times = Time::where('service_id', $serviceId)
            ->where('day_of_week',$dayOfWeek)
            ->distinct()
            ->pluck('day_of_week');
            $exceptions = Exception::where('service_id', $serviceId)
             ->where('date',$date)
            ->where('is_available',1)
            ->distinct()
            ->pluck('date');
        if ($times->isNotEmpty()) {
            $timesArr[] =  $date->format('Y-m-d');
        }
        if ($exceptions->isNotEmpty()) {
            $exceptionsArr[] =  $date->format('Y-m-d');
        }
        }
        $data =array_merge($timesArr,$exceptionsArr);
        return["data"=>array_values($data)];
    }

public function getAvailableSlots($serviceId, $date)
{
    $date = Carbon::parse($date);
    $day = $date->dayOfWeek;

    // 1. نجيب الاستثناءات الخاصة باليوم ده
    $exceptions = Exception::where('service_id', $serviceId)
        ->whereDate('date', $date->format('Y-m-d'))
        ->get();

    // 2. لو فيه استثناء بـ is_available = 0 → اليوم كله مقفول
    if ($exceptions->where('is_available', 0)->isNotEmpty()) {
        return [
            'date' => $date->format('Y-m-d'),
            'dayOfWeek' => (string)$day,
            'available' => false,
            'totalAvailableSlots' => 0,
            'availableTimeSlots' => [],
        ];
    }

    $availableSlots = [];

    // 3. لو فيه استثناءات مفتوحة is_available = 1 → نولد الـ slots منها
    if ($exceptions->where('is_available', 1)->isNotEmpty()) {
        foreach ($exceptions->where('is_available', 1) as $exception) {
            $availableSlots = array_merge(
                $availableSlots,
                $this->generateSlotsFromException($exception, $date->format('Y-m-d'), $serviceId)
            );
        }
    }

    // 4. نجيب الـ Times العادية (لو مفيش استثناء قافل اليوم)
    $serviceTimes = Time::where('service_id', $serviceId)
        ->where('day_of_week', $day)
        ->get();

    if ($serviceTimes->isNotEmpty()) {
        $availableSlots = array_merge(
            $availableSlots,
            $this->getAvailableSlotsForDate(
                $serviceId,
                $date->format('Y-m-d'),
                $serviceTimes
            )
        );
    }

    // 5. لو مفيش أي Slots → اليوم كله مقفول
    if (empty($availableSlots)) {
        return [
            'date' => $date->format('Y-m-d'),
            'dayOfWeek' => (string)$day,
            'available' => false,
            'totalAvailableSlots' => 0,
            'availableTimeSlots' => [],
        ];
    }

    // 6. نرجع الدمج
    return [
        'date' => $date->format('Y-m-d'),
        'dayOfWeek' => (string)$day,
        'available' => true,
        'totalAvailableSlots' => count($availableSlots),
        'availableTimeSlots' => $availableSlots,
    ];
}


    private function getAvailableSlotsForDate($serviceId, $date, $serviceTimes = null)
    {
        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek ;

        // فحص الاستثناءات أولاً
        $exception = Exception::where('service_id', $serviceId)
            ->where('date', $date->format('Y-m-d'))
            ->first();

        if ($exception && !$exception->is_available) {
            return []; // اليوم غير متاح بسبب استثناء
        }

        // جلب أوقات الخدمة إذا لم تُمرر
        if (!$serviceTimes) {
            $serviceTimes = Time::where('service_id', $serviceId)
                ->where('day_of_week', $dayOfWeek)
                ->get();
        }

        $bookedAppointments = Appointment::where(function($query) {
            $query->where('status', AppointmentStatusEnum::APPROVED)
                  ->orWhere(function($subQuery) {
                      $subQuery->where('status', AppointmentStatusEnum::PENDING)
                               ->where('created_at', '>', now()->subHours(24));
                  });
        })
        ->where('date', $date->format('Y-m-d'))
        ->get(['start_at', 'end_at']);
        // جلب المواعيد المحجوزة لهذا التاريخ
        // $bookedAppointments = Appointment::where('service_id', $serviceId)
        // ->whereIn('status', [AppointmentStatusEnum::APPROVED, AppointmentStatusEnum::PENDING])
        // ->where('created_at', '>', now()->subHours(24))
        // ->where('date', $date->format('Y-m-d'))
        // ->get(['start_at', 'end_at']);

        $availableSlots = [];

        foreach ($serviceTimes as $serviceTime) {
            $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $serviceTime->start_time);
            $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $serviceTime->end_time);
            $sessionDuration = $serviceTime->session_time; // بالدقائق

            // تقسيم الفترة إلى جلسات
            $currentTime = $startTime->copy();
            while ($currentTime->lt($endTime)) {
                $sessionEnd = $currentTime->copy()->addMinutes($sessionDuration);

                if ($sessionEnd->lte($endTime)) {
                    // فحص إذا كانت الجلسة محجوزة
                    $isBooked = $bookedAppointments->contains(function ($booking) use ($currentTime, $sessionEnd, $date) {
                        $bookingStart = Carbon::parse($date->format('Y-m-d') . ' ' . $booking->start_at);
                        $bookingEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $booking->end_at);

                        // فحص التداخل
                        return ($currentTime->lt($bookingEnd) && $sessionEnd->gt($bookingStart));
                    });

                    // إضافة الفترة فقط إذا لم تكن محجوزة
                    if (!$isBooked) {
                        $availableSlots[] = [
                            'startTime' => $currentTime->format('H:i'),
                            'endTime' => $sessionEnd->format('H:i'),
                            // 'duration' => $sessionDuration
                        ];
                    }
                }

                $currentTime->addMinutes($sessionDuration);
            }
        }

        return $availableSlots;
    }
    protected function generateSlotsFromException($exception, $date, $serviceId)
    {
        $slots = [];

        $start = Carbon::parse($exception->start_time);
        $end   = Carbon::parse($exception->end_time);

        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes($exception->session_time);

            if ($slotEnd->gt($end)) {
                break;
            }

            // نتأكد إن الـ slot ده مش محجوز بالفعل في appointments
            $isBooked = Appointment::where('service_id', $serviceId)
                ->whereDate('date', $date)
                ->whereTime('start_at', $start->format('H:i:s'))
                ->exists();

            if (!$isBooked) {
                $slots[] = [
                    'startTime' => $start->format('H:i'),
                    'endTime'   => $slotEnd->format('H:i'),
                ];
            }

            $start->addMinutes($exception->session_time);
        }

        return $slots;
    }

}
