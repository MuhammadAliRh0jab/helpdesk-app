<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Aduan;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class AduanTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'bootstrap'; // Remove if using Tailwind

    // Re-enable the updatingSearch method for auto-search
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Get the logged-in user's ID
        $user = Auth::user();

        // Eager load the 'service' relationship and filter by user_id
        $aduans = Ticket::with('service')
            ->where('user_id', $user->id) // Filter by logged-in user
            ->where('ticket_code', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('livewire.aduan-table', compact('aduans'));
    }
}