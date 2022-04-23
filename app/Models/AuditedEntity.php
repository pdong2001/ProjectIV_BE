<?php
namespace App\Models;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Eloquent\Model;

class AuditedEntity extends Model
{
    public $timestamps = true;

    public const FILLABLE = [
        'created_by','last_updated_by'
    ];

    public static function Migration(Blueprint $table) : void
    {
        $table->id();
        $table->unsignedBigInteger('created_by');
        $table->unsignedBigInteger('last_updated_by')->nullable();
        $table->string('note')->nullable();
        $table->timestamps();
    }

    public static function tArray($model)
    {
        return [
            'note' => $model->note,
            'created_by' => $model->created_by,
            'created_at' => $model->created_at,
            'updated_by' => $model->last_updated_by,
            'updated_at' => $model->updated_at
        ];
    }
}