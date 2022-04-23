<?php

namespace App\Enums;

enum Status: int
{
    case Pending = 1;
    case Accepted = 2;
    case Shipping = 3;
    case Completed = 4;
    case Rejected = 5;
    case Canceled = 6;
    case PendingRefund = 7;
    case AcceptedRefund = 8;
    case Refunded = 9;
    case RefundRejected = 10;
}

