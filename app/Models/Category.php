<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends AuditedEntity
{
    use HasFactory, FullTextSearch;
    protected $table = 'categories';
    // public string $name;

    public const RULES = [
        'name' => 'required|unique:categories'
    ];

    protected $searchable = [
        'name'
    ];
    protected $fillable = [
        ...parent::FILLABLE,
        'name',
        'visible'
    ];
    protected $casts = [
        'visible' => 'boolean',
      ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}