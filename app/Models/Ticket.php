<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'unit_id', 'ticket_code', 'title', 'description', 'status', 'rating', 'created_at', 'updated_at'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function responses()
    {
        return $this->hasMany(TicketResponse::class);
    }

    public function uploads()
    {
        return $this->hasMany(TicketUpload::class);
    }

    public function pics()
    {
        return $this->belongsToMany(Pic::class, 'ticket_pic', 'ticket_id', 'pic_id')
                    ->withPivot('pic_stats')
                    ->wherePivot('pic_stats', 'active')
                    ->withTimestamps();
    }
}