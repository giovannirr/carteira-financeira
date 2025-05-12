<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class WalletBalance extends Component
{
    public $balance;

    protected $listeners = ['refresh-wallet' => 'refreshBalance'];

    public function mount()
    {
        $this->refreshBalance();
    }

    public function refreshBalance()
    {
        $this->balance = Auth::user()->wallet->fresh()->balance ?? 0;
    }

    public function render()
    {
        return view('livewire.wallet-balance');
    }
}
