<div>
    @if (! $open)
        <button 
            class="text-blue-600 underline"
            wire:click="$set('open', true)">
            Solicitar reversão
        </button>
    @else
        <div class="border rounded p-4 bg-gray-50 mt-2">
            <form wire:submit.prevent="submit" class="space-y-4">
                <p class="text-sm text-gray-600">
                    Você está solicitando a reversão da transação #{{ $transaction->id }}. Informe o motivo abaixo:
                </p>

                <textarea
                    wire:model.defer="message"
                    rows="4"
                    placeholder="Descreva o motivo..."
                    class="w-full border rounded px-3 py-2 mt-1"
                ></textarea>
                @error('message') 
                    <span class="text-red-600 text-sm">{{ $message }}</span> 
                @enderror

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-green-600 px-4 py-2 rounded hover:bg-green-700">
                        Enviar Solicitação
                    </button>

                    <button type="button" class="text-gray-600 underline" wire:click="$set('open', false)">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>