<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    protected $fillable = [
        'services_id', 'user_id', 'pic_start', 'pic_end', 'pic_desc', 'pic_stats', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'services_id');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_pic', 'pic_id', 'ticket_id')
                    ->withPivot('pic_stats')
                    ->wherePivot('pic_stats', 'active')
                    ->withTimestamps();
    }
}