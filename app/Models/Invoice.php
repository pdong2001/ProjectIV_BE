<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends AuditedEntity
{
    use HasStatus, HasFactory;
    protected $table = 'invoices';
    // public int $user_id;
    // public float $total;
    // public float $paid;
    // public int $status;

    public const RULES = [
        'customer_id' => 'required_unless:customer_name,null',
        'customer_name' => 'required_unless:customer_id,null',
        'address' => 'required_unless:customer_id,null',
        'phone_number' => 'required_unless:customer_id,null'
    ];

    protected $fillable = [
        ...parent::FILLABLE,
        'customer_id',
        'total',
        'paid',
        'customer_name',
        'address',
        'phone_number',
        'province',
        'district',
        'commune',
        'option_count',
        'cancel_pending'
    ];

    public static function getStatusName(int $status)
    {
        switch ($status) {
            case 1:
                return "Đang xử lý";
            case 2:
                return "Đã chấp nhận";
            case 3:
                return "Đang chuẩn bị";
            case 4:
                return "Đang giao";
            case 5:
                return "Hoàn tất";
            case 6:
                return "Từ chối";
            case 7:
                return "Hủy";
            case 8:
                return "Trả hàng";
            default:
                return "Đang xử lý";
        }
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
