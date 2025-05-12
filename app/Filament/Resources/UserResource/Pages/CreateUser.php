<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public static function afterCreate(Form $form): void
    {
        $record = $form->getModelInstance();
        $role = $form->getState()['role'] ?? 'user';

        $record->syncRoles([$role]);
    }
}
