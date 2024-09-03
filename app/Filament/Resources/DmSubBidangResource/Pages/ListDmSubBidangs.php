<?php

namespace App\Filament\Resources\DmSubBidangResource\Pages;

use App\Filament\Resources\DmSubBidangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDmSubBidangs extends ListRecords
{
    protected static string $resource = DmSubBidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->icon('heroicon-o-plus'),
        ];
    }
}
