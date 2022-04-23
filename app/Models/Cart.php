<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    public const RULES = [
        'customer_id' => 'required',
        'quantity' => 'min:0',
        'product_detail_id' => 'required',
    ];

    protected $fillable = [
        'customer_id',
        'quantity',
        'product_detail_id'
    ];

    public function productDetail()
    {
        return $this->hasOne(ProductDetail::class, 'id', 'product_detail_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
