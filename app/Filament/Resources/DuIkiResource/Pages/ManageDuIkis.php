<?php

namespace App\Filament\Resources\DuIkiResource\Pages;

use App\Filament\Resources\DuIkiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDuIkis extends ManageRecords
{
    protected static string $resource = DuIkiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Tambah'),
        ];
    }
}
