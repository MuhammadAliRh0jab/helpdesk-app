<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use App\Models\Pic;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $roleId = $user->role_id;

        // Cek peran yang diizinkan berdasarkan parameter $roles
        if (!in_array($roleId, array_map('intval', $roles))) {
            abort(403, 'Unauthorized action.');
        }

        // Logika spesifik untuk setiap peran
        if ($roleId == 3) { // Pegawai (PIC)
            // Ambil parameter 'ticket' dari route, bisa berupa instance Ticket karena route model binding
            $ticket = $request->route('ticket');

            // Jika tidak ada instance Ticket, coba ambil ticket_id dari input (misalnya dari form)
            if (!$ticket instanceof Ticket) {
                $ticketId = $request->input('ticket_id');

                if ($ticketId) {
                    $ticket = Ticket::find($ticketId);

                    if (!$ticket) {
                        abort(404, 'Tiket tidak ditemukan.');
                    }
                }
            }

            if ($ticket instanceof Ticket) {
                // Verifikasi apakah PIC ditugaskan ke tiket ini
                if (!$this->isPicAssignedToTicket($user->id, $ticket)) {
                    abort(403, 'Anda belum ditugaskan sebagai PIC untuk tiket ini.');
                }
            } else {
                // Jika tidak ada tiket spesifik, batasi seperti Warga biasa kecuali sudah ditugaskan
                if (!Pic::where('user_id', $user->id)->where('pic_stats', 'active')->exists()) {
                    return redirect()->route('tickets.index')->with('error', 'Anda hanya dapat membuat atau melihat aduan seperti Warga biasa.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Memeriksa apakah seorang PIC ditugaskan ke tiket tertentu
     */
    private function isPicAssignedToTicket($userId, $ticket)
    {
        // Pastikan $ticket adalah instance model yang benar
        if (!$ticket instanceof Ticket) {
            Log::error('Invalid ticket instance in isPicAssignedToTicket: ' . (is_object($ticket) ? get_class($ticket) : gettype($ticket)));
            return false;
        }

        return Pic::where('user_id', $userId)
            ->where('pic_stats', 'active')
            ->whereHas('tickets', function ($query) use ($ticket) {
                $query->where('tickets.id', $ticket->id); // Tambahkan prefiks tabel untuk menghindari ambiguitas
            })
            ->exists();
    }
}