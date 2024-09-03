<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuIkiResource\Pages;
use App\Filament\Resources\DuIkiResource\RelationManagers;
use App\Models\dm_sub_bidang;
use App\Models\DuIki;
use App\Models\Tahun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;

class DuIkiResource extends Resource
{
    protected static ?string $model = DuIki::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Data Dukung';
    protected static ?string $modelLabel = 'Indikator Kinerja Individu';
    protected static ?string $pluralModelLabel = 'Indikator Kinerja Individu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
                Forms\Components\Hidden::make('sub_bidang_id')
                    ->default(Auth::user()->sub_bidang_id)
                    ->live()
                    ->nullable(),
                Forms\Components\Select::make('tahun_id')
                    ->options(function (): array {
                        return Tahun::where('status', '=',  '1')->pluck('nama', 'id')->toArray();
                    })
                    ->columnSpan(2)
                    ->label('Tahun')
                    ->live()
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule 
                            ->where('tahun_id', $get('tahun_id'))
                            ->where('user_id', $get('user_id'));
                    }, ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Iki anda sudah ada sebelumnya.',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('pdf')
                    ->nullable()
                    ->columnSpan(2)
                    ->acceptedFileTypes(['application/pdf'])
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => Auth::user()->name . '.pdf')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'iki/' . 
                        Tahun::find($get('tahun_id'))->nama . 
                        '/' . 
                        dm_sub_bidang::find($get('sub_bidang_id'))->nama),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum ada data')
            ->groups([
                Group::make('user.subBidang.dm_bidang.nama')
                    ->label('Bidang')
                    ->collapsible()
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('user.subBidang.dm_bidang.nama')
            ->columns([
                Tables\Columns\TextColumn::make('tahun.nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\IconColumn::make('pdf')
                    ->boolean()
                    ->default(0)
                    ->url(fn ($record) => $record->pdf ? Storage::url($record->pdf) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum diverifikasi' => 'gray',
                        'perlu perbaikan' => 'warning',
                        'telah diverifikasi' => 'success',
                    }),
                Tables\Columns\TextColumn::make('keterangan')
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\Action::make('Verifikasi')
                    ->iconButton()
                    ->color('success')
                    ->icon('heroicon-o-paper-clip')
                    ->hidden(fn ($record) => !Auth::user()->hasRole(['super_admin','admin','verifikator']))
                    ->fillForm(fn (DuIki $record): array => [
                        'status' => $record->status,
                        'keterangan' => $record->keterangan
                    ])
                    ->Form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'telah diverifikasi' => 'Terverifikasi',
                                'perlu perbaikan' => 'Perlu Perbaikan',
                            ]),
                        Forms\Components\Textarea::make('keterangan'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                            'keterangan' => $data['keterangan'],
                        ]);
                    }),
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => Auth::id() !== $record->user_id)
                    ->iconButton()
                    ->color('warning')
                    ->mutateFormDataUsing(function (array $data, $record) {
                        if (isset($data['pdf']) && $data['pdf'] !== $record->pdf) {
                            if ($record->pdf) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($record->pdf);
                            }
                        }
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => Auth::id() !== $record->user_id)
                    ->iconButton()
                    ->before(function ($record) {
                        if ($record->pdf) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->pdf);
                        }
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDuIkis::route('/'),
        ];
    }
}
