<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Filament\Resources\WalletResource\RelationManagers;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use App\Services\WalletService;
use Filament\Tables\Actions\Action;
use App\Models\Transaction;
use App\Models\User;
use Filament\Notifications\Notification;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $modelLabel = 'Carteira';
    protected static ?string $pluralModelLabel = 'Carteiras';

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
                TextColumn::make('user.name')->label('Usuário')->searchable(),
                TextColumn::make('balance')->money('BRL')->label('Saldo'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Ação de depósito
                Action::make('deposit')
                    ->label('Depositar')
                    ->modalHeading('Realizar Depósito')
                    ->form([
                        TextInput::make('amount')
                            ->label('Valor a ser depositado')
                            ->numeric()
                            ->minValue(0.01)
                            ->required(),
                    ])
                    ->action(function ($record, array $data, $livewire) {
                        try {
                            $walletService = app(WalletService::class);
                            $walletService->deposit($record->user, (float) $data['amount']);

                            \Filament\Notifications\Notification::make()
                                ->title('Depósito realizado com sucesso')
                                ->success()
                                ->send();

                        } catch (\Illuminate\Validation\ValidationException $e) {
                            foreach ($e->errors() as $field => $messages) {
                                foreach ($messages as $message) {
                                    $livewire->addError($field, $message);
                                }
                            }
                        }
                    }),
            
                // Ação de transferência
                Action::make('transfer')
                    ->label('Transferir')
                    ->modalHeading('Transferir para outro usuário')
                    ->form([
                        TextInput::make('recipient_email')
                            ->label('E-mail do destinatário')
                            ->email()
                            ->required(),
                        TextInput::make('amount')
                            ->label('Valor a ser transferido')
                            ->numeric()
                            ->minValue(0.01)
                            ->required(),
                    ])
                    ->action(function ($record, array $data, $livewire) {
                        try {
                            $walletService = app(WalletService::class);
                            $walletService->transfer(
                                $record->user,
                                $data['recipient_email'],
                                (float) $data['amount']
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Transferência realizada com sucesso')
                                ->success()
                                ->send();

                        } catch (\Illuminate\Validation\ValidationException $e) {
                            foreach ($e->errors() as $field => $messages) {
                                foreach ($messages as $message) {
                                    $livewire->addError($field, $message);
                                }
                            }
                        } catch (\Exception $e) {
                            $livewire->addError('recipient_email', $e->getMessage());
                        }
                    }),
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
            'index' => Pages\ListWallets::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
}
