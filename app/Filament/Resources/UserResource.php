<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('Senha'),
                Select::make('role')
                    ->label('Tipo de usuário')
                    ->options(Role::pluck('name', 'name')->toArray())
                    ->required()
                    ->default('user') // usado na criação
                    ->dehydrated() // somente quando o valor mudar
                    ->afterStateHydrated(function (Select $component, $state, $record) {
                        $component->state($record->roles->pluck('name')->first());
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')->label('Função'),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
                TextColumn::make('wallet.balance')
                    ->label('Saldo')
                    ->money('BRL'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function afterCreate(Form $form): void
    {
        $record = $form->getModelInstance(); // pega o modelo recém-criado (User)
        $role = $form->getState()['role']; // pega o valor do campo "role" do formulário
        $record->assignRole($role); // atribui essa role ao usuário
    }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return Auth::user()?->hasRole('admin');
    // }
}
