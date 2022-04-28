<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailOptionValue extends Model
{
    use HasFactory;
    public $table = 'product_detail_option_values';
    public $timestamps = false;
    public const RULES = [
        'product_detail_id' => 'required',
        'value' => 'required',
        'option_id' => 'required'
    ];

    protected $fillable = [
        'product_detail_id',
        'value',
        'option_id'
    ];

    public function productDetai()
    {
        return $this->belongsTo(ProductDetail::class, 'id', 'product_detail_id');
    }

    public function option()
    {
        return $this->hasOne(ProductOption::class, 'id', 'option_id');
    }
}
