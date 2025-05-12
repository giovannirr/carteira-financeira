<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\WalletService;
use Illuminate\Validation\ValidationException;

class DepositForm extends Component
{

    public float $amount = 0;
    public bool $confirming = false; // controla exibição do modal

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
    ];

    protected WalletService $walletService;

    public function boot(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function confirm()
    {
        $this->validate();
        $this->confirming = true;
    }

    public function deposit()
    {
        try {
            $this->walletService->deposit(Auth::user(), $this->amount);

            $this->reset(['amount', 'confirming']);

            $this->dispatch('refresh-wallet', message: 'Depósito realizado com sucesso.');
        } catch (ValidationException $e) {
            $this->addError('amount', $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.deposit-form');
    }
}
