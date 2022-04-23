<?php

namespace App\Traits;

use App\Enums\Status;

trait HasStatus
{
    public function setStatus(Status $st):void
    {
        $this->status = $st;
    }
    public function getStatusDescription():string
    {
        return $this->status?StatusDescription[$this->status]:'';
    }
}

const StatusDescription = [
    1 => 'Chờ xác nhận',
    2 => 'Đã xác nhận',
    3 => 'Đang giao hàng',
    4 => 'Đã hoàn thành',
    5 => 'Đã từ chối',
    6 => 'Đã hủy',
    7 => 'Chờ xác nhận hoàn trả',
    8 => 'Đã đồng ý hoàn trả',
    9 => 'Đã hoàn trả',
    10 => 'Đã từ chối yêu cầu hoàn trả'
];
