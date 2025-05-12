<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWallet extends EditRecord
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    // Impedir acesso a edição via URL
    public static function canAccess(array $parameters = []): bool
    {
        return false;
    }
}
