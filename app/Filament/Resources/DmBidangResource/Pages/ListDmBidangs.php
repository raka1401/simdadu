<?php

namespace App\Filament\Resources\DmBidangResource\Pages;

use App\Filament\Resources\DmBidangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use PhpParser\Node\Stmt\Label;

class ListDmBidangs extends ListRecords
{
    protected static string $resource = DmBidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->Label('Tambah')
                ->icon('heroicon-o-plus'),
        ];
    }
}
