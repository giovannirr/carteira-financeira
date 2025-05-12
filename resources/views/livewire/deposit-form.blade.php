<div class="max-w-md mx-auto mt-6 p-4 bg-white shadow rounded">
    <h2 class="text-lg font-semibold mb-4">Fazer Depósito</h2>
    <form wire:submit.prevent="confirm">
        <div class="mb-4">
            <label class="block text-sm font-medium">Valor (R$)</label>
            <input type="number" wire:model.defer="amount" step="0.01" class="w-full border rounded px-3 py-2 mt-1" />
            @error('amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" style="background-color: #ccc; padding: 8px 16px; border: none; border-radius: 4px;">
            Depositar
        </button>
    </form>

    @if ($confirming)
    <div 
        x-data="{ show: true }" 
        x-show="show"
        x-transition.opacity.duration.300ms
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div 
            x-show="show"
            x-transition.duration.300ms
            class="bg-white p-6 rounded shadow-md w-[90%] max-w-md"
        >
            <h3 class="text-lg font-semibold mb-4">Confirmar Depósito</h3>
            <p class="mb-4">Tem certeza que deseja depositar R$ {{ number_format($amount, 2, ',', '.') }}?</p>
            <div class="flex justify-end space-x-2">
                <button 
                    wire:click="$set('confirming', false)" 
                    x-on:click="show = false"
                    class="px-4 py-2 bg-gray-300 rounded"
                >
                    Cancelar
                </button>
                <button wire:click="deposit" class="px-4 py-2 bg-green-600 text-black rounded">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Script JS para mostrar alerta --}}
    <script>
        window.addEventListener('refresh-wallet', event => {
            alert(event.detail.message);
        });
    </script>
</div>