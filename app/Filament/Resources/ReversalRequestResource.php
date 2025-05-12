<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReversalRequestResource\Pages;
use App\Filament\Resources\ReversalRequestResource\RelationManagers;
use App\Models\ReversalRequest;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class ReversalRequestResource extends Resource
{
    protected static ?string $model = ReversalRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $modelLabel = 'Solicitação de Reversão';
    protected static ?string $pluralModelLabel = 'Solicitações de Reversão';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('message')->label('Mensagem')->disabled(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pendente',
                        'approved' => 'Aprovada',
                        'rejected' => 'Rejeitada',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Usuário'),
                TextColumn::make('transaction.id')->label('Transação ID'),
                TextColumn::make('message')->label('Mensagem')->wrap(),
                TextColumn::make('status')->badge()
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Solicitado em')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('aprovar')
                    ->label('Aprovar e Reverter')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ReversalRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (ReversalRequest $record) {
                        // Reversão lógica (pode ser extraída para service depois)
                        $tx = $record->transaction;

                        if ($tx->reversedTransaction) {
                            return;
                        }

                        $wallet = $tx->wallet;
                        $related = $tx->relatedWallet;

                        // Cria as  duas reversões (retira de onde foi  e add de onde veio)
                        $wallet->transactions()->create([
                            'type' => 'reversal',
                            'amount' => -$tx->amount,
                            'related_wallet_id' => $related->id,
                            'reversed_transaction_id' => $tx->id,
                        ]);

                        $related->transactions()->create([
                            'type' => 'reversal',
                            'amount' => $tx->amount,
                            'related_wallet_id' => $wallet->id,
                            'reversed_transaction_id' => $tx->id,
                        ]);

                        $wallet->decrement('balance', $tx->amount);
                        $related->increment('balance', $tx->amount);
                        
                        //Altera o status para aprovado
                        $record->update(['status' => 'approved']);
                    }),

                Action::make('rejeitar')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ReversalRequest $record) => $record->status === 'pending')
                    ->action(fn (ReversalRequest $record) => $record->update(['status' => 'rejected'])), //Altera o status para rejeitado
                ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReversalRequests::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return ReversalRequest::where('status', 'pending')->count() ?: null;
    }
}
