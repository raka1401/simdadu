<?php

namespace App\Filament\Resources\DuEvalinResource\Pages;

use App\Filament\Resources\DuEvalinResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDuEvalins extends ManageRecords
{
    protected static string $resource = DuEvalinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Tambah'),
        ];
    }
}
