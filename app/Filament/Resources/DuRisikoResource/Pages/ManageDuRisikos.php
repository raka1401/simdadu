<?php

namespace App\Filament\Resources\DuRisikoResource\Pages;

use App\Filament\Resources\DuRisikoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDuRisikos extends ManageRecords
{
    protected static string $resource = DuRisikoResource::class;

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
