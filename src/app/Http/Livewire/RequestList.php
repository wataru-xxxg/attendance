<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CorrectionRequest;
use Illuminate\Support\Facades\Auth;

class RequestList extends Component
{
    public $correctionRequests;

    public $tab = 'pending';

    public function mount()
    {
        $this->correctionRequests = CorrectionRequest::where('user_id', Auth::user()->id)->where('approved', 0)->get();
    }

    public function switchTab($tab)
    {
        $this->tab = $tab;
        if ($this->tab == 'pending') {
            $this->correctionRequests = CorrectionRequest::where('user_id', Auth::user()->id)->where('approved', 0)->get();
        } else {
            $this->correctionRequests = CorrectionRequest::where('user_id', Auth::user()->id)->where('approved', 1)->get();
        }
    }

    public function render()
    {
        return view('livewire.request-list');
    }
}
