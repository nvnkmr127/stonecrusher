<?php

namespace App\Enums;

enum PaymentMode: string
{
    case CASH = 'Cash';
    case BANK_TRANSFER = 'Bank Transfer';
    case UPI = 'UPI';
    case CHECK = 'Check';
    case OTHER = 'Other';
}
