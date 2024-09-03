<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DmSubBidangResource\Pages;
use App\Filament\Resources\DmSubBidangResource\RelationManagers;
use App\Filament\Resources\DmSubBidangResource\RelationManagers\JenisDokumenRelationManager;
use App\Models\dm_sub_bidang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DmSubBidangResource extends Resource
{
    protected static ?string $model = dm_sub_bidang::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $label = 'Sub Bidang';
    protected static ?string $pluralLabel = 'Sub Bidang';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dm_bidang_id')
                    ->label('Bidang')
                    ->relationship('dm_bidang', 'nama')
                    // ->options(dm_bidang::all()->pluck('nama', 'id'))
                    // ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Bidang')
                            ->required(),
                        Forms\Components\TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('nama')
                    ->label('Sub Bidang')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Tidak Ada Data')
            ->groups([
                Group::make('dm_bidang.nama')
                    ->label('Bidang')
                    ->collapsible()
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('dm_bidang.nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Sub Bidang'),
                Tables\Columns\TextColumn::make('dm_jenis_dokumen_count')
                    ->badge()
                    ->label('Jumlah Dokumen')
                    ->color('success')
                    ->counts('dm_jenis_dokumen')
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
            JenisDokumenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDmSubBidangs::route('/'),
            'create' => Pages\CreateDmSubBidang::route('/create'),
            'edit' => Pages\EditDmSubBidang::route('/{record}/edit'),
        ];
    }
}
