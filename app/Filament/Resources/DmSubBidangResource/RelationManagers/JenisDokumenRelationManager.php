<?php

namespace App\Filament\Resources\DmSubBidangResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisDokumenRelationManager extends RelationManager
{
    protected static string $relationship = 'dm_jenis_dokumen';
    protected static ?string $pluralLabel = 'Jenis Dokumen';
    protected static ?string $title = 'Jenis Dokumen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->emptyStateHeading('Tidak Ada Data')
            ->emptyStateDescription('Tambahkan Jenis Dokumen')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Jenis Dokumen'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Tambah'),
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
