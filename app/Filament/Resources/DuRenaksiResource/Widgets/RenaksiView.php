<?php

namespace App\Filament\Resources\DuRenaksiResource\Widgets;

use App\Models\dm_sub_bidang;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RenaksiView extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Rencana Aksi';

    

    public function table(Table $table): Table
    {
        return $table
            ->query(   
                dm_sub_bidang::query()->whereHas('du_renaksi', function ($query) {
                    $query->whereHas('tahun', function ($subQuery){
                        $subQuery->where('nama', '=', date('Y'));
                    });
                })
            )
            ->groups([
                Group::make('dm_bidang.nama')
                    ->label('Bidang')
                    ->collapsible()
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('dm_bidang.nama')
            ->columns([
                Tables\Columns\TextColumn::make('du_renaksi.subBidang.nama')
                    ->label('Sub Bidang'),
                Tables\Columns\TextColumn::make('du_renaksi.judul')
                    ->label('Judul Dokumen'),
                Tables\Columns\IconColumn::make('du_renaksi.pdf')
                    ->label('PDF')
                    ->default('0')
                    ->boolean(),
                Tables\Columns\IconColumn::make('du_renaksi.excel')
                    ->label('Excel')
                    ->default('0')
                    ->boolean(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
