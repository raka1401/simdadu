<?php

namespace App\Filament\Resources\DmKegiatanResource\Pages;

use App\Filament\Resources\DmKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDmKegiatans extends ListRecords
{
    protected static string $resource = DmKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Tambah'),
        ];
    }
}
