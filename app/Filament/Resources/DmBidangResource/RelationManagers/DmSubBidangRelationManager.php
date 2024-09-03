<?php

namespace App\Filament\Resources\DmBidangResource\RelationManagers;

use App\Models\dm_sub_bidang;
use App\Models\DmJenisDokumen;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DmSubBidangRelationManager extends RelationManager
{
    protected static string $relationship = 'dm_sub_bidang';

    protected static ?string $pluralLabel = 'Data sub bidang';
    protected static ?string $title = 'Data Sub Bidang';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Sub Bidang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('keterangan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Sub Bidang'),
                Tables\Columns\TextColumn::make('keterangan'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Tambah Sub Bidang'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
