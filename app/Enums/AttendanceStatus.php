<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case LATE = 'late';
    case HALF_DAY = 'half_day';
    case ABSENT = 'absent';
    case LEAVE = 'leave';
}
