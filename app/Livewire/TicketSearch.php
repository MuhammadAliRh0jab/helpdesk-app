<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketSearch extends Component
{
    use WithPagination;
    
    public $search = '';
    protected $paginationTheme = 'bootstrap';
    
    // Auto-reset page saat pencarian berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    
    public function render()
    {
        $user = Auth::user();
        
        $tickets = Ticket::with(['service', 'unit'])
            ->where('user_id', $user->id)
            ->where(function($query) {
                $query->where('ticket_code', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);
            
        return view('livewire.ticket-search', compact('tickets'));
    }
}