<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Filament\Resources\UsersResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nip')
                    ->label('NIP/NIK')
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->required(),
                Forms\Components\Select::make('sub_bidang_id')
                    ->label('Bidang')
                    ->nullable()
                    ->relationship('subBidang', 'nama'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(false)
                    ->maxLength(255),
                Forms\Components\CheckboxList::make('roles')
                    ->required()
                    ->relationship('roles', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                
                Tables\Columns\TextColumn::make('subBidang.nama')
                    ->label('Bidang'),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP/NIK'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(function ($record) {
                        return match ($record->roles->first()->name) {
                            'super_admin' => 'success',
                            'Kabid' => 'danger',
                            'jafung' => 'warning',
                            'admin' => 'primary',
                            'verifikator' => 'secondary',
                            'staf' => 'info',
                        };
                    }),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
