<?php

namespace App\Filament\Resources\DuRenaksiResource\Pages;

use App\Filament\Resources\DuRenaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDuRenaksis extends ManageRecords
{
    protected static string $resource = DuRenaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->createAnother(false)
                ->icon('heroicon-o-plus'),
        ];
    }
}
