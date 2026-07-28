<?php

namespace App\Enums;

enum ReportPeriodStatus: string
{
    case Open = 'open';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Delivered = 'delivered';
    case Locked = 'locked';
}
