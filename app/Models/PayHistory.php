<?php
namespace App\Models;

class PayHistory extends AuditedEntity{
    protected $table = 'pay_histories';
    // public int $customer_id;
    // public int $money;
    public function user()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}