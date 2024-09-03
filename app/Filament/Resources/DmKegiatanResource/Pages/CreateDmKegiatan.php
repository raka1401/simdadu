<?php

namespace App\Filament\Resources\DmKegiatanResource\Pages;

use App\Filament\Resources\DmKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDmKegiatan extends CreateRecord
{
    protected static string $resource = DmKegiatanResource::class;
    protected static ?string $title = 'Tambah Kegiatan';
}
