<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends FullAuditedEntity
{
    use HasFactory;

    protected $table = 'customers';

    public const RULES = [
        'name' => 'required',
        'phone_number' => 'required',
    ];

    protected $fillable = [
        ...parent::FILLABLE,
        'name',
        'address',
        'phone_number',
        'debt',
        'birth',
        'bank_number',
        'bank_name',
        'user_id',
        'province',
        'commune',
        'blob_id',
        'district'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id','user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'customer_id', 'id');
    }

    public function image() 
    {
        return $this->hasOne(Blob::class, 'id', 'blob_id');
    }
}
