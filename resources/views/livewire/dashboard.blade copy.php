<div>
    <h1 class="text-xl font-bold mb-4">Minha Carteira</h1>
    @php
        $wallet = auth()->user()->wallet;
    @endphp

    @if ($wallet)
        <p><strong>Saldo atual:</strong> R$ {{ number_format($wallet->balance, 2, ',', '.') }}</p>

        {{-- Formulário de Depósito --}}
        <div class="mt-6">
            <livewire:deposit-form />
        </div>

        <div class="mt-6">
            <livewire:transfer-form />
        </div>

        <h2 class="text-lg mt-6 font-semibold">Transações</h2>
        <ul>
            @foreach ($wallet->transactions as $tx)
                <li class="border-b py-2">
                    <strong>{{ ucfirst($tx->type) }}</strong> - 
                    R$ {{ number_format($tx->amount, 2, ',', '.') }} 
                    @if($tx->relatedWallet)
                        com {{ $tx->relatedWallet->user->name }}
                    @endif
                    em {{ $tx->created_at->format('d/m/Y H:i') }}

                    {{-- Mostrar botão apenas se não for reversal e ainda não tiver solicitação --}}
                    @if (! $tx->reversedTransaction && $tx->type !== 'reversal' && ! $tx->reversalRequest)
                        <livewire:request-reversal-form :transaction="$tx" :wire:key="'reversal-'.$tx->id" />
                    @elseif ($tx->reversalRequest)
                        <span class="text-yellow-600 ml-2">(Reversão solicitada)</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <div class="p-4 text-red-600 border border-red-300 bg-red-100 rounded">
            Este usuário ainda não possui uma carteira financeira.
        </div>
    @endif
</div>
