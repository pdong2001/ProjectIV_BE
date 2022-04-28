<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;
    public $table = 'product_options';
    public $timestamps = false;

    public const RULES = [
        'product_id' => 'required',
        'name' => 'required',
    ];

    protected $fillable = [
        'product_id',
        'name'
    ];

    public function values()
    {
        return $this->hasMany(ProductDetailOptionValue::class, 'option_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }
}
