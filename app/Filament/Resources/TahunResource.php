<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunResource\Pages;
use App\Filament\Resources\TahunResource\RelationManagers;
use App\Models\Tahun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
// use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\Modal\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Support\Arr;

class TahunResource extends Resource
{
    protected static ?string $model = Tahun::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Tahun';
    protected static ?string $pluralModelLabel = 'Data Tahun';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Tahun')
                    ->numeric()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Data yang diinput tidak boleh sama guys !!!!',
                        'numeric' => 'Inputan harus berupa angka',
                        'required' => 'Inputan ini wajib diisi',
                    ]),
                Forms\Components\Select::make('status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->RecordTitle('Data Tahun')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->sortable()
                    ->label('Tahun'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),
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
            'index' => Pages\ManageTahuns::route('/'),
        ];
    }
}
