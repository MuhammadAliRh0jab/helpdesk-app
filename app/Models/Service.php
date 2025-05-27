<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'unit_id',
        'svc_name',
        'svc_desc',
        'svc_icon',
        'category_id',
        'allow_guest',
        'status',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function pics()
    {
        return $this->hasMany(Pic::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Automatically generate UUID when creating a new service
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Uuid::uuid4()->toString();
            }
        });
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'service_id');
    }
}