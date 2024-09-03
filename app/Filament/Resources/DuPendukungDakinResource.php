<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuPendukungDakinResource\Pages;
use App\Filament\Resources\DuPendukungDakinResource\RelationManagers;
use App\Models\dm_bidang;
use App\Models\dm_sub_bidang;
use App\Models\DmJenisDokumen;
use App\Models\DmPerangkatDaerah;
use App\Models\DuPendukungDakin;
use App\Models\Tahun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class DuPendukungDakinResource extends Resource
{
    protected static ?string $model = DuPendukungDakin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Dukung';
    protected static ?string $modelLabel = 'Dokumen Pendukung Dakin';
    protected static ?string $pluralModelLabel = 'Dokumen Pendukung Dakin';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
                Forms\Components\Hidden::make('sub_bidang_id')
                    ->default(Auth::user()->sub_bidang_id)
                    ->live(),
                Forms\Components\Select::make('tahun_id')
                    ->label('Tahun')
                    ->options(function (): array {
                        return Tahun::where('status', '=',  '1')->pluck('nama', 'id')->toArray();
                    }),
                Forms\Components\Select::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->live()
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule 
                            ->where('tahun_id', $get('tahun_id'))
                            ->where('jenis_dokumen_id', $get('jenis_dokumen_id'))
                            ->where('perangkat_daerah_id', $get('perangkat_daerah_id'));
                    }, ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Jenis Dokumen yang anda pilih sudah ada.',
                    ])
                    ->options(DmJenisDokumen::where('sub_bidang_id', '=', Auth::user()->sub_bidang_id)->pluck('nama', 'id')),
                Forms\Components\Select::make('perangkat_daerah_id')
                    ->label('Perangkat Daerah')
                    ->nullable()
                    // ->default('-')
                    ->live()
                    ->columnSpan(2)
                    ->relationship('perangkat_daerah', 'nama')
                    ->visible(fn (Get $get): bool => $get('jenis_dokumen_id') && DmJenisDokumen::find($get('jenis_dokumen_id'))->perangkat_daerah == 1),
                Forms\Components\FileUpload::make('pdf')
                    ->nullable()
                    ->columnSpan(2)
                    ->acceptedFileTypes(['application/pdf'])
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => DmJenisDokumen::find($get('jenis_dokumen_id'))->nama . '.pdf')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'data dukung dakin/' . 
                        Tahun::find($get('tahun_id'))->nama 
                         . '/' .
                        dm_bidang::find(dm_sub_bidang::find($get('sub_bidang_id'))->dm_bidang_id)->nama .'/'. 
                        dm_sub_bidang::find($get('sub_bidang_id'))->nama
                        .'/'. ($get('perangkat_daerah_id') ? DmPerangkatDaerah::find($get('perangkat_daerah_id'))->nama : '-')
                    ),
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
                Tables\Columns\TextColumn::make('user.subBidang.nama')
                    ->label('Bidang'),
                Tables\Columns\TextColumn::make('tahun.nama'),
                Tables\Columns\TextColumn::make('jenis_dokumen.nama')
                    ->label('Jenis Dokumen'),
                Tables\Columns\TextColumn::make('perangkat_daerah.nama')
                    ->label('Perangkat Daerah')
                    ->default('-'),
                Tables\Columns\IconColumn::make('pdf')
                    ->default(0)
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum diverifikasi' => 'gray',
                        'perlu perbaikan' => 'warning',
                        'telah diverifikasi' => 'success',
                    }),
                Tables\Columns\TextColumn::make('keterangan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Verifikasi')
                    ->iconButton()
                    ->color('success')
                    ->icon('heroicon-o-paper-clip')
                    ->hidden(fn ($record) => !Auth::user()->hasRole(['super_admin','admin','verifikator']))
                    ->fillForm(fn (DuPendukungDakin $record): array => [
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
                        if (isset($data['excel']) && $data['excel'] !== $record->excel) {
                            if ($record->excel) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($record->excel);
                            }
                        }
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->hidden(fn ($record) => Auth::id() !== $record->user_id)
                    ->before(function ($record) {
                        if ($record->pdf) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->pdf);
                        }
                        if ($record->excel) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->excel);
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
            'index' => Pages\ManageDuPendukungDakins::route('/'),
        ];
    }
}
