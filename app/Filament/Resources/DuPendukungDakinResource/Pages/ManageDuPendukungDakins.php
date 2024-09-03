<?php

namespace App\Filament\Resources\DuPendukungDakinResource\Pages;

use App\Filament\Resources\DuPendukungDakinResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDuPendukungDakins extends ManageRecords
{
    protected static string $resource = DuPendukungDakinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Tambah'),
        ];
    }
}
