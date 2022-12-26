<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductDetail extends FullAuditedEntity
{
    use HasFactory;
    protected $table = 'product_details';
    // public int $product_id;
    // public string $option_name = '';
    // public string $option_value = '';
    // public float $out_price = 0;
    // public float $in_price = 0;
    // public int $remaining_quantity = 0;
    // public int $total_quantity = 0;
    // public ?int $default_image = null;
    // public array $images;

    public const RULES = [
        'product_id' => 'required',
        'out_price' => 'required|min:0',
        'unit' => 'required',
        'options' => 'required'
    ];

    protected $casts = [
        'visible' => 'boolean'
    ];

    public $fillable = [
        ...parent::FILLABLE,
        "id",
        "out_price",
        "in_price",
        "remaining_quantity",
        "total_quantity",
        "default_image",
        "product_id",
        "unit",
        "visible"
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function images()
    {
        return $this->morphMany(ImageAssign::class, 'imageable', 'imageable_type', 'imageable_id', 'id');
    }

    public function image()
    {
        return $this->hasOne(Blob::class, 'id', 'default_image');
    }

    public function options()
    {
        return $this->hasMany(ProductDetailOptionValue::class, 'product_detail_id', 'id');
    }
}
