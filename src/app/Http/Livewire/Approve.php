<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CorrectionRequest;

class Approve extends Component
{
    public $correctionRequestId;
    public $correctionRequest;
    public $buttonText;
    public $buttonClass;

    public function mount($correctionRequestId)
    {
        $this->correctionRequest = CorrectionRequest::find($correctionRequestId);
        if ($this->correctionRequest->approved == 1) {
            $this->buttonText = '承認済み';
            $this->buttonClass = 'approved disabled';
        } else {
            $this->buttonText = '承認';
        }
    }

    public function approve()
    {
        $this->correctionRequest->approved = 1;
        $this->correctionRequest->save();
        $this->buttonText = '承認済み';
        $this->buttonClass = 'approved disabled';
    }

    public function render()
    {
        return view('livewire.approve');
    }
}
