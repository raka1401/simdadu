<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DmJenisDokumenResource\Pages;
use App\Filament\Resources\DmJenisDokumenResource\RelationManagers;
use App\Models\dm_sub_bidang;
use App\Models\DmJenisDokumen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DmJenisDokumenResource extends Resource
{
    protected static ?string $model = DmJenisDokumen::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Master Data';
    // protected static ?string $recordTitleAttribute = 'tai';
    protected static ?string $navigationLabel = 'Jenis Dokumen';
    protected static ?string $label = 'Jenis Dokumen';
    protected static ?string $pluralLabel = 'Data Jenis Dokumen';
    // protected static ?string $badgeTooltip = 'There are new posts';
    // protected static ?string $title = 'Posts';


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('sub_bidang_id')
                ->options(dm_sub_bidang::all()->pluck('nama', 'id'))
                ->label('Sub Bidang')
                ->searchable(),
            Forms\Components\TextInput::make('nama')
                ->label('Jenis Dokumen')
                ->required(),
                // ->columnSpan('full'),
            Forms\Components\Toggle::make('perangkat_daerah')
            ]);
        }
        
        public static function table(Table $table): Table
        {
        return $table
            ->recordTitle('Jenis Dokumen')
            ->columns([
                Tables\Columns\TextColumn::make('subBidang.nama')
                    ->label('Sub Bidang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Jenis Dokumen')
                    ->sortable(),
                Tables\Columns\IconColumn::make('perangkat_daerah')
                    ->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDmJenisDokumens::route('/'),
        ];
    }
}
