<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponseUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_response_id',
        'uuid',
        'filename_ori',
        'filename_path',
    ];

    public function response()
    {
        return $this->belongsTo(TicketResponse::class);
    }
}