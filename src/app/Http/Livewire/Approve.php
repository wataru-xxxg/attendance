<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CorrectionRequest;

class Approve extends Component
{
    public $correctionRequestId;
    public $buttonText;
    public $buttonClass;

    public function mount($correctionRequestId)
    {
        $this->correctionRequestId = $correctionRequestId;
        $this->buttonText = '承認';
    }

    public function approve()
    {
        $correctionRequest = CorrectionRequest::find($this->correctionRequestId);
        $correctionRequest->approved = 1;
        $correctionRequest->save();
        $this->buttonText = '承認済み';
        $this->buttonClass = 'approved disabled';
    }

    public function render()
    {
        return view('livewire.approve');
    }
}
