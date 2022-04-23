<?php
namespace App\Models;
class ReceiptDetail extends AuditedEntity {
    protected $table = 'receipt_details';
    public int $quantity;
    public float $price;
    public int $product_detail_id;
    public int $receipt_id;
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
    public function productDetail()
    {
        return $this->hasOne(ProductDetail::class, 'id', 'product_detail_id');
    }
}