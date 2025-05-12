<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function deposit(User $user, float $amount): void
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'O valor do depósito deve ser maior que zero.',
            ]);
        }

        $wallet = $user->wallet;

        if (! $wallet) {
            throw ValidationException::withMessages([
                'wallet' => 'Usuário não possui carteira ativa.',
            ]);
        }

        DB::transaction(function () use ($wallet, $amount) {
            $wallet->increment('balance', $amount);

            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $amount,
            ]);
        });
    }

    public function transfer(User $sender, string $recipientEmail, float $amount): void
    {
        $recipient = User::where('email', $recipientEmail)
        ->where('id', '!=', $sender->id)
        ->whereHas('wallet')
        ->first();

        if (! $recipient) {
            throw ValidationException::withMessages([
                'recipient_email' => 'Usuário destinatário inválido ou sem carteira.',
            ]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'O valor da transferência deve ser maior que zero.',
            ]);
        }

        $senderWallet = $sender->wallet;
        $recipientWallet = $recipient->wallet;

        if (! $senderWallet || ! $recipientWallet) {
            throw ValidationException::withMessages([
                'wallet' => 'Remetente ou destinatário não possui carteira ativa.',
            ]);
        }

        if ($senderWallet->balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => 'Saldo insuficiente para transferência.',
            ]);
        }

        DB::transaction(function () use ($senderWallet, $recipientWallet, $amount) {
            // Atualiza os saldos
            $senderWallet->decrement('balance', $amount);
            $recipientWallet->increment('balance', $amount);

            // Cria transações
            Transaction::create([
                'wallet_id' => $senderWallet->id,
                'related_wallet_id' => $recipientWallet->id,
                'type' => 'transfer',
                'amount' => -$amount,
            ]);

            Transaction::create([
                'wallet_id' => $recipientWallet->id,
                'related_wallet_id' => $senderWallet->id,
                'type' => 'transfer',
                'amount' => $amount,
            ]);
        });
    }
}