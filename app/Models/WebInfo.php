<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebInfo extends Model
{
    use HasFactory;

    protected $table = 'web_infos';

    public const RULES = [
        'name' => 'required'
    ];

    protected $fillable = [
        'content',
        'title',
        'blob_id',
        'name',
        'link',
        'icon'
    ];

    public function image()
    {
        return $this->hasOne(Blob::class, 'id', 'blob_id');
    }
}
