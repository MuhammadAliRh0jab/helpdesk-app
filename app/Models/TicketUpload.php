<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'uuid',
        'filename_ori',
        'filename_path',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}