<?php

namespace App\Filament\Resources\DmSubBidangResource\Pages;

use App\Filament\Resources\DmSubBidangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDmSubBidang extends EditRecord
{
    protected static string $resource = DmSubBidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
