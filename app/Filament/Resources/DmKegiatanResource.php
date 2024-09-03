<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DmKegiatanResource\Pages;
use App\Filament\Resources\DmKegiatanResource\RelationManagers;
use App\Models\DmKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DmKegiatanResource extends Resource
{
    protected static ?string $model = DmKegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kegiatan';
    protected static ?string $modelLable = 'Kegiatan';
    protected static ?string $label = 'Kegiatan';
    protected static ?string $pluralModelLabel = 'Data Kegiatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->label('Kode'),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->label('Nama Kegiatan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Kegiatan'),
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
            RelationManagers\DmSubKegiatanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDmKegiatans::route('/'),
            'create' => Pages\CreateDmKegiatan::route('/create'),
            'edit' => Pages\EditDmKegiatan::route('/{record}/edit'),
        ];
    }
}
