<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blob extends AuditedEntity
{
    use HasFactory;
    protected $table = 'blobs';

    protected $fillable = [
        ...parent::FILLABLE,
        'name',
        'file_path'
    ];

    public function assigns()
    {
        return $this->hasMany(ImageAssign::class, 'blob_id', 'id');
    }
}