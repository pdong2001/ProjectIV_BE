<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends FullAuditedEntity
{
    use HasFactory;

    protected $table = 'customers';
    protected $fillable = [
        ...parent::FILLABLE,
        'name',
        'address',
        'phone_number',
        'dept',
        'birth',
        'bank_number',
        'bank_name'
    ];
}
