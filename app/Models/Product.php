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
    // public ?int $provider_id;
    // public string $name;
    // public array $details;
    // public array $images;
    // public ?int $category_id;
    // public ?int $default_image;
    // public string $code;
    // public int $option_count;

    // public function __construct(array $data = [])
    // {
    //     if ($data)
    //     {
    //         $this->provider_id = $data['provider_id']??null;
    //         $this->name = $data['name']??'';
    //         $this->code = $data['provider_id']??null;
    //         $this->category_id = $data['category_id']??null;
    //         $this->default_image = $data['default_image']??null;
    //         $this->category_id = $data['category_id']??null;
    //         $this->option_count = $data['option_count']??0;
    //     }
    // }

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
        'default_detail'
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
}