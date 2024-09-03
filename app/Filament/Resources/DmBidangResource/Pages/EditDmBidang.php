<?php

namespace App\Filament\Resources\DmBidangResource\Pages;

use App\Filament\Resources\DmBidangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDmBidang extends EditRecord
{
    protected static string $resource = DmBidangResource::class;
    protected static ?string $title = 'Edit Data Bidang';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }
}
