<?php

namespace App\Filament\Resources\TahunResource\Pages;

use App\Filament\Resources\TahunResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTahuns extends ManageRecords
{
    protected static string $resource = TahunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->CreateAnother(false)
                ->ModalHeading('Tambah Data Tahun')
                ->label('Tambah')
                ->icon('heroicon-o-plus')
                // ->iconButton('heroicon-m-magnifying-glass'),
        ];
    }
}
