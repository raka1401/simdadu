<?php

namespace App\Filament\Resources\DmKegiatanResource\Pages;

use App\Filament\Resources\DmKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDmKegiatan extends EditRecord
{
    protected static string $resource = DmKegiatanResource::class;
    protected static ?string $title = 'Edit Kegiatan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
