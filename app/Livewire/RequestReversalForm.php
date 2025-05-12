<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ReversalRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class RequestReversalForm extends Component
{
    public Transaction $transaction;
    public string $message = '';
    public bool $open = false;

    protected $rules = [
        'message' => 'required|string|min:10|max:1000',
    ];

    public function submit()
    {
        $this->validate();

        ReversalRequest::create([
            'transaction_id' => $this->transaction->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        $this->reset(['message', 'open']);

        session()->flash('status', 'Solicitação de reversão enviada com sucesso!');
        $this->dispatch('reversal-requested');
    }

    public function render()
    {
        return view('livewire.request-reversal-form');
    }
}
