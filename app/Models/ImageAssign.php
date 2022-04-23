<?php
namespace App\Models;

use App\Public\Enum\EntityType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageAssign extends Model {
    use HasFactory;
    protected $table = 'image_assigns';
    // public int $blob_id;
    // public int $imageable_id;
    // public string $imageable_type;

    protected $fillable = [
        'blob_id',
        'imageable_id',
        'imageable_type'
    ];

    public function imageable()
    {
        return $this->morphTo('imageable', 'imageable_type', 'imageable_id', 'id');
    }

    public function blob()
    {
        return $this->hasOne(Blob::class, 'id', 'blob_id');
    }
}