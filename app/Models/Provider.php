<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends AuditedEntity
{
    use HasFactory;
    protected $table = 'providers';
    public string $name;
    public string $address;
    public string $phone;
    public function product()
    {
        return $this->hasMany(Product::class, 'provider_id', 'id');
    }
}