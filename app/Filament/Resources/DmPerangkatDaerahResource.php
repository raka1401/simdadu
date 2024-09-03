<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DmPerangkatDaerahResource\Pages;
use App\Filament\Resources\DmPerangkatDaerahResource\RelationManagers;
use App\Models\DmPerangkatDaerah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DmPerangkatDaerahResource extends Resource
{
    protected static ?string $model = DmPerangkatDaerah::class;
    // protected static ?string $modelLabel = 'Perangkat Daerah';
    protected static ?string $pluralModelLabel = 'Data Perangkat Daerah';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Perangkat Daerah';
    protected static ?string $label = 'Perangkat Daerah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Perangkat Daerah')
                    ->required()
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitle('Perangkat Daerah')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Pernagkat Daerah'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDmPerangkatDaerahs::route('/'),
        ];
    }
}
