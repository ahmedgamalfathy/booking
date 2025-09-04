<?php
namespace App\Filters\Appointment;
use Carbon\Carbon;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterByPeriod  implements Filter
{
 public function __invoke(Builder $query, $value, string $property): Builder
  {
        // لو مفيش قيمة نرجع زي ما هو
        if (!$value) return $query;
        if (!$value) {
            $date   = Carbon::now();
        } else {
            if (is_array($value)) {
                $date   = isset($value['date']) ? Carbon::parse($value['date']) : Carbon::now();
            } else {
                $date   = Carbon::parse($value);
            }
        }
// dd($value);
        switch ($property) {
            case 'day':
                $direction = $value['direction'] ?? 'both';
                if ($direction === 'before') {
                    $start = $date->copy()->subDay()->startOfDay();
                    $end   = $date->copy()->endOfDay();
                } elseif ($direction === 'after') {
                    $start = $date->copy()->startOfDay();
                    $end   = $date->copy()->addDay()->endOfDay();
                } else { // startOfDay() ,>endOfDay()
                    $start = $date->copy()->subDay();
                    $end   = $date->copy()->addDay();
                }
                break;

            case 'week':
                $direction = $value['direction'] ?? 'both';

                if ($direction === 'before') {
                    $start = $date->copy()->startOfWeek()->subWeek();
                    $end   = $date->copy()->endOfWeek();
                } elseif ($direction === 'after') {
                    $start = $date->copy()->startOfWeek();
                    $end   = $date->copy()->endOfWeek()->addWeek();
                } else { // both ,subWeek() ,addWeek()
                    $start = $date->copy()->startOfWeek();
                    $end   = $date->copy()->endOfWeek();
                }
                break;

            case 'month':
                $direction = $value['direction'] ?? 'both';
                if ($direction === 'before') {
                    $start = $date->copy()->startOfMonth()->subMonth();
                    $end   = $date->copy()->endOfMonth();
                } elseif ($direction === 'after') {
                    $start = $date->copy()->startOfMonth();
                    $end   = $date->copy()->endOfMonth()->addMonth();
                } else { // both , subMonth(), addMonth()
                    $start = $date->copy()->startOfMonth();
                    $end   = $date->copy()->endOfMonth();
                }
                break;

            case 'custom':
                if (is_array($value) && isset($value['start'], $value['end'])) {
                    $start = Carbon::parse($value['start'])->startOfDay();
                    $end   = Carbon::parse($value['end'])->endOfDay();
                } else {
                    return $query;
                }
                break;

            default:
                return $query;
        }

        return $query->whereBetween('date', [$start, $end]);
    }
}

