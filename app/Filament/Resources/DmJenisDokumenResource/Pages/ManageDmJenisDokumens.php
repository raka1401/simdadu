<?php

namespace App\Filament\Resources\DmJenisDokumenResource\Pages;

use App\Filament\Resources\DmJenisDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDmJenisDokumens extends ManageRecords
{
    protected static string $resource = DmJenisDokumenResource::class;
    protected static ?string $title = 'Data Jenis Dokumen';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->CreateAnother(false)
                ->ModalHeading('Tambah Data Jenis Dokumen')
                ->icon('heroicon-o-plus')
                ->label('Tambah'),
        ];
    }
}
