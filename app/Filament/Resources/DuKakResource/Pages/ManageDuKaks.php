<?php

namespace App\Filament\Resources\DuKakResource\Pages;

use App\Filament\Resources\DuKakResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDuKaks extends ManageRecords
{
    protected static string $resource = DuKakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Tambah'),
        ];
    }
}
