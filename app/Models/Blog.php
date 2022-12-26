<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    public const RULES = [
        'title' => 'required',
    ];
    protected $table = 'blogs';
    protected $fillable = [
        'title',
        'content',
        'image_id',
        'short_description'
    ];
    public function Image()
    {
        return $this->hasOne(Blob::class, 'id', 'image_id');
    }
}
