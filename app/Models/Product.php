<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends FullAuditedEntity
{
    use HasFactory;

    public const RULES = [
        'name' => 'required',
    ];
    protected $table = 'products';

    protected $fillable = [
        "provider_id",
        "name",
        "code",
        "default_image",
        "category_id",
        "option_count",
        "created_by",
        "last_updated_by",
        "deleted_time",
        "deleted_by",
        "is_deleted",
        "description",
        'visible',
        'default_detail',
        'min_price',
        'max_price'
    ];

    protected $casts = [
        'visible' => 'boolean'
    ];

    public function details()
    {

        return $this->hasMany(ProductDetail::class);
    }

    public function image()
    {
        return $this->hasOne(Blob::class,'id', 'default_image');
    }

    public function images()
    {
        return $this->morphMany(ImageAssign::class, 'imageable', 'imageable_type', 'imageable_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class, 'product_id', 'id');
    }
}