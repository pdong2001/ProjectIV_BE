<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceDetail extends AuditedEntity {
    use HasFactory;
    protected $table = 'invoice_details';
    // public int $product_detail_id;
    // public int $quantity;
    // public int $price;
    // public int $invoice_id;

    public const RULES = [
        'product_detail_id' => 'required',
        'quantity' => 'required',
        'invoice_id' => 'required'
    ];

    protected $fillable = [
        ...parent::FILLABLE,
        'product_detail_id',
        'invoice_id',
        'price',
        'quantity'
    ];
    
    public function productDetail()
    {
        return $this->hasOne(ProductDetail::class, 'id', 'product_detail_id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}