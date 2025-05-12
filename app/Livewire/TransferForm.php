<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Services\WalletService;

class TransferForm extends Component
{
    public float $amount = 0;
    public string $recipient_email = '';
    public ?string $recipient_name = null;
    public bool $confirming = false;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'recipient_email' => 'required|email|exists:users,email',
    ];

    protected WalletService $walletService;

    public function boot(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function confirm()
    {
        $this->validate();

        // Verificação para impedir transferência para si mesmo
        if (Auth::user()->email === $this->recipient_email) {
            $this->addError('recipient_email', 'Você não pode transferir para si mesmo.');
            return;
        }

        $this->confirming = true;
    }

    public function transfer()
    {

        $sender = Auth::user();

        try {
            $this->walletService->transfer($sender, $this->recipient_email, $this->amount);

            $this->reset(['amount', 'recipient_email', 'recipient_name', 'confirming']);

            $this->dispatch('refresh-wallet', message: 'Transferência realizada com sucesso.');
        } catch (ValidationException $e) {
            $this->addError('recipient_email', $e->getMessage());
        }
    }

    // Método reativo para capturar o nome
    public function updatedRecipientEmail($value)
    {
        $this->recipient_name = null;

        $user = User::where('email', $value)
            ->where('id', '!=', Auth::id())
            ->whereHas('wallet')
            ->first();

        if ($user) {
            $this->recipient_name = $user->name;
        }
    }

    public function render()
    {
        return view('livewire.transfer-form');
    }
}
