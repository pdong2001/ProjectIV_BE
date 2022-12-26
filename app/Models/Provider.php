<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends AuditedEntity
{
    use HasFactory;
    protected $table = 'providers';

    public const RULES = [
        'name' => 'required',
        'phone' => 'required',
        'visible' => 'required|boolean'
    ];

    protected $fillable = [
        ...parent::FILLABLE,
        'name',
        'address',
        'phone',
        'visible',
        'file_path'
    ];
    protected $casts = [
        'visible' => 'boolean',
    ];

    public function product()
    {
        return $this->hasMany(Product::class, 'provider_id', 'id');
    }
}