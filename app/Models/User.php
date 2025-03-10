<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'unit_id',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function responses()
    {
        return $this->hasMany(TicketResponse::class);
    }

    public function pics()
    {
        return $this->hasMany(Pic::class, 'user_id');
    }

    public function ticketsAsPic()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_pic', 'pic_id', 'ticket_id')
            ->using(Pic::class)
            ->wherePivot('pic_stats', 'active')
            ->withPivot('pic_stats', 'updated_at')
            ->withTimestamps()
            ->whereHas('pics', function ($query) {
                $query->where('user_id', $this->id)->where('pic_stats', 'active');
            });
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getUserFunction()
    {
        switch ($this->role_id) {
            case 1:
                return "Memiliki akses penuh ke sistem: mengelola pengguna, unit, layanan, dan tiket.";
            case 2:
                return "Mengelola tiket pengaduan, menugaskan PIC, dan memantau status tiket.";
            case 3:
                return "Menangani respons tiket pengaduan, memberikan update status, dan mengunggah dokumen.";
            case 4:
                return "Membuat tiket pengaduan dan melacak status tiket mereka.";
            default:
                return "Fungsi belum ditentukan.";
        }
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function isAssignedAsPic()
    {
        $exists = DB::table('pics')
            ->join('ticket_pic', 'pics.id', '=', 'ticket_pic.pic_id')
            ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
            ->where('pics.user_id', $this->id)
            ->where('ticket_pic.pic_stats', 'active')
            ->whereNull('tickets.deleted_at')
            ->exists();
            return $exists;
    }
}