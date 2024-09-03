<?php

namespace App\Filament\Resources\DmBidangResource\Pages;

use App\Filament\Resources\DmBidangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDmBidang extends CreateRecord
{
    protected static string $resource = DmBidangResource::class;

    protected static ?string $title = 'Tambah Data Bidang';
}
