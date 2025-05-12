<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $wallet = Auth::user()->wallet()->with('transactions.relatedWallet.user')->first();

        return view('livewire.dashboard', [
            'wallet' => $wallet,
        ]);
    }
}
