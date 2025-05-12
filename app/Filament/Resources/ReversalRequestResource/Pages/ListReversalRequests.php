<?php

namespace App\Filament\Resources\ReversalRequestResource\Pages;

use App\Filament\Resources\ReversalRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReversalRequests extends ListRecords
{
    protected static string $resource = ReversalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
