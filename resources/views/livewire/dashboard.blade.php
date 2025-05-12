<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Minha Carteira</h1>

    @php
        $wallet = auth()->user()->wallet;
    @endphp

    @if ($wallet)
        <livewire:wallet-balance />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Depósito</h2>
                <livewire:deposit-form />
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Transferência</h2>
                <livewire:transfer-form />
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Transações</h2>
            <ul class="divide-y divide-gray-200">
                @foreach ($wallet->transactions as $tx)
                    <li class="py-4">
                        <p>
                            <strong>{{ ucfirst($tx->type) }}</strong> - 
                            R$ {{ number_format($tx->amount, 2, ',', '.') }}
                            @if ($tx->relatedWallet)
                                com {{ $tx->relatedWallet->user->name }}
                            @endif
                            em {{ $tx->created_at->format('d/m/Y H:i') }}
                        </p>

                        @if (! $tx->reversedTransaction && $tx->type !== 'reversal' && ! $tx->reversalRequest)
                            <div class="mt-2">
                                <livewire:request-reversal-form :transaction="$tx" :wire:key="'reversal-'.$tx->id" />
                            </div>
                        @elseif ($tx->reversalRequest)
                            <span class="text-yellow-600 ml-2">(Reversão solicitada)</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="p-4 text-red-600 border border-red-300 bg-red-100 rounded">
            Este usuário ainda não possui uma carteira financeira.
        </div>
    @endif
</div>