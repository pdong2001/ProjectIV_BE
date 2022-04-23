<?php
namespace App\Models;

class Receipt extends AuditedEntity {
    protected $table = 'receipts';
    // public int $provider_id;
    // public int $paid;
    // public int $total;
    public function details()
    {
        return $this->hasMany(ReceiptDetail::class, 'receipt_id', 'id');
    }
}