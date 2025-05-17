<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPic extends Model
{
    protected $table = 'ticket_pic';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'ticket_id',
        'pic_id',
        'pic_stats',
    ];

    protected $attributes = [
        'pic_stats' => 'active',
    ];

    protected $casts = [
        'ticket_id' => 'integer',
        'pic_id' => 'integer',
        'pic_stats' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke tabel Ticket (jika ada model Ticket)
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    // Relasi ke tabel PIC/User (jika pic_id mengacu ke tabel user atau staff)
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
