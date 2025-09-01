<?php
namespace App\Enums;
enum DayOfWeekEnum:int{
    case STARDAY = 1;
    case SUNDAY = 2;
    case MONDAY = 3;
    case TUESDAY = 4;
    case WEDNESDAY = 5;
    case THURSDAY = 6;
    case FRIDAY = 7;    

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}
