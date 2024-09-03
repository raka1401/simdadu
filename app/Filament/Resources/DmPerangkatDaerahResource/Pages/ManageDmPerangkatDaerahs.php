<?php

namespace App\Filament\Resources\DmPerangkatDaerahResource\Pages;

use App\Filament\Resources\DmPerangkatDaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDmPerangkatDaerahs extends ManageRecords
{
    protected static string $resource = DmPerangkatDaerahResource::class;
    protected static ?string $title = 'Data Perangkat Daerah';
    // protected static ?string 

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Tambah')
                ->modalHeading('Tambah Data Perangkat Daerah')
                ->createAnother(false),
        ];
    }
}
