<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DmBidangResource\Pages;
use App\Filament\Resources\DmBidangResource\RelationManagers;
use App\Filament\Resources\DmBidangResource\RelationManagers\DmSubBidangRelationManager;
use App\Models\Dm_Bidang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DmBidangResource extends Resource
{
    protected static ?string $model = Dm_Bidang::class;
    // protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-in';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Bidang';
    protected static ?string $pluralModelLabel = 'Data bidang';
    protected static ?string $label = 'Bidang';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Bidang')
                    ->required(),
                Forms\Components\TextInput::make('keterangan')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Bidang'),
                Tables\Columns\TextColumn::make('keterangan'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            DmSubBidangRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDmBidangs::route('/'),
            'create' => Pages\CreateDmBidang::route('/create'),
            'edit' => Pages\EditDmBidang::route('/{record}/edit'),
        ];
    }
}
