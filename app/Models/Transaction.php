<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'related_wallet_id',
        'reversed_transaction_id'
    ];

    // A carteira principal que realizou a transação.
    public function wallet(): BelongsTo {
        return $this->belongsTo(Wallet::class);
    }

    // A carteira relacionada (destinatário no caso de transferência).
    public function relatedWallet(): BelongsTo {
        return $this->belongsTo(Wallet::class, 'related_wallet_id');
    }

    // Se essa transação é uma reversão de outra.
    public function reversedTransaction(): BelongsTo {
        return $this->belongsTo(Transaction::class, 'reversed_transaction_id');
    }


    // Vínculo com o pedido de reversão feito pelo usuário.
    public function reversalRequest(): HasOne
    {
        return $this->hasOne(ReversalRequest::class);
    }
}
