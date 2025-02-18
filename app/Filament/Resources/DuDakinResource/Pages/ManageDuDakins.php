<?php

namespace App\Filament\Resources\DuDakinResource\Pages;

use App\Filament\Resources\DuDakinResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDuDakins extends ManageRecords
{
    protected static string $resource = DuDakinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->createAnother(false)
                ->label('Tambah'),
        ];
    }
}
