<?php

namespace App\Rules;

use Closure;
use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;

class SessionTimeValidation implements ValidationRule
{
    private string $startTime;
    private string $endTime;

    public function __construct(string $startTime, string $endTime)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->startTime || !$this->endTime || !$value) {
            $fail("The entered values ​​are invalid.");
            return;
        }
        try {
            $start = Carbon::createFromFormat('H:i', $this->startTime);
            $end   = Carbon::createFromFormat('H:i', $this->endTime);

            if ($end->lessThanOrEqualTo($start)) {
                $fail("The end time must be after the start time.");
                return;
            }

            $totalMinutes = $start->diffInMinutes($end);
            if ($value > $totalMinutes) {
                $fail("The session time must be less than the total time period.");
            }
            // if ($totalMinutes % $value !== 0 ) {
            //     $fail("The session duration should be divided equally.");
            // }
        } catch (Exception $e) {
            $fail("The time format is incorrect (should be H:i).");
        }

    }
}
