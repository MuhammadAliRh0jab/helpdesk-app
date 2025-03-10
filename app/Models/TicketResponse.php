<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'ticket_id_quote', // Tetap gunakan nama kolom ini
        'user_id',
        'message',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quotedResponse()
    {
        return $this->belongsTo(TicketResponse::class, 'ticket_id_quote'); // Relasi ke respons yang dikutip
    }

    public function uploads()
    {
        return $this->hasMany(TicketResponseUpload::class);
    }
}