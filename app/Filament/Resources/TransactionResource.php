<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Transação';
    protected static ?string $pluralModelLabel = 'Transações';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('wallet.user.name')->label('Usuário')->searchable(),
                TextColumn::make('type')->badge()->colors([
                    'success' => 'deposit',
                    'warning' => 'transfer',
                    'danger' => 'reversal',
                ]),
                TextColumn::make('amount')->money('BRL'),
                TextColumn::make('relatedWallet.user.name')->label('Relacionado')->default('-'),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
                TextColumn::make('reversedTransaction')
                    ->label('Revertida?')
                    ->formatStateUsing(fn ($state) => $state ? 'Sim' : 'Não')
                    ->badge()
                    ->colors([
                        'danger' => fn ($state) => $state !== null,
                        'gray' => fn ($state) => $state === null,
                    ]),
            ])
            ->filters([
                SelectFilter::make('reversed')
                    ->label('Revertida?')
                    ->options([
                        'yes' => 'Sim',
                        'no' => 'Não',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'yes' => $query->whereNotNull('reversed_transaction_id'),
                            'no'  => $query->whereNull('reversed_transaction_id'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),

                Action::make('revert')
                    ->label('Reverter')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->visible(fn (Transaction $record) => $record->type !== 'reversal' && !$record->reversedTransaction)
                    ->action(function (Transaction $record) {
                        $wallet = $record->wallet;
                        $relatedWallet = $record->relatedWallet;

                        if (!$relatedWallet) {
                            Notification::make()
                                ->title('Transação não possui carteira relacionada.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Cria reversões
                        Transaction::create([
                            'wallet_id' => $wallet->id,
                            'related_wallet_id' => $relatedWallet->id,
                            'type' => 'reversal',
                            'amount' => -$record->amount,
                            'reversed_transaction_id' => $record->id,
                        ]);

                        Transaction::create([
                            'wallet_id' => $relatedWallet->id,
                            'related_wallet_id' => $wallet->id,
                            'type' => 'reversal',
                            'amount' => $record->amount,
                            'reversed_transaction_id' => $record->id,
                        ]);

                        // Atualiza saldos
                        $wallet->increment('balance', -$record->amount);
                        $relatedWallet->increment('balance', $record->amount);

                        Notification::make()
                            ->title('Transação revertida com sucesso.')
                            ->success()
                            ->send();
                    }),
                ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
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
            'index' => Pages\ListTransactions::route('/'),
            //'create' => Pages\CreateTransaction::route('/create'),
            //'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
