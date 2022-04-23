<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Schema\Blueprint;


class FullAuditedEntity extends AuditedEntity
{
    public const FILLABLE = [
        ...parent::FILLABLE,
        'deleted_time',
        'deleted_by',
        'is_deleted'
    ];
    protected $cast = [
        'is_deleted' => 'boolean'
    ];

    public static function Migration(Blueprint $table): void
    {
        parent::Migration($table);
        $table->timestamp('deleted_time')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();
        $table->boolean('is_deleted')->default(false);
    }
    public function sortDelete(): bool
    {
        $this->is_deleted = true;
        $this->deleted_time = now();
        return $this->save();
    }
    public static function tArray($model)
    {
        return [
            ...parent::tArray($model),
            'is_deleted' => $model->is_deleted,
            'deleted_by' => $model->deleted_by,
            'deleted_time' => $model->deleted_time
        ];
    }
}
