<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuKakResource\Pages;
use App\Filament\Resources\DuKakResource\RelationManagers;
use App\Models\dm_sub_bidang;
use App\Models\DmSubKegiatan;
use App\Models\DuKak;
use App\Models\Tahun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;

class DuKakResource extends Resource
{
    protected static ?string $model = DuKak::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationGroup = 'Data Dukung';
    protected static ?string $label = 'Kerangka Acuan Kerja';
    protected static ?string $pluralModelLabel = 'Kerangka Acuan Kerja';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
                Forms\Components\Hidden::make('sub_bidang_id')
                    ->default(Auth::user()->sub_bidang_id),
                Forms\Components\Select::make('tahun_id')
                    ->relationship('tahun', 'nama' , function (Builder $query) {
                        $query->where('status', '=',  '1');
                    })
                    ->label('Tahun')
                    ->required()
                    ->live(),
                Forms\Components\Select::make('jenis_kak')
                    ->label('Jenis KAK')
                    ->required()
                    ->options([
                        'kak usulan' => 'KAK Usulan',
                        'kak kegiatan' => 'KAK Kegiatan',
                    ]),
                Forms\Components\Select::make('sub_kegiatan_id')
                    ->options(DmSubKegiatan::all()->pluck('nama', 'id'))
                    ->label('Sub Kegiatan')
                    ->columnSpan(2)
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule
                        ->where('tahun_id', $get('tahun_id'))
                        ->where('jenis_kak', $get('jenis_kak'))
                        ->where('sub_kegiatan_id', $get('sub_kegiatan_id'));
                    })
                    ->validationMessages([
                        'unique' => 'KAK pada kegiatan tersebut sudah ada.',
                    ])
                    ->searchable()
                    ->live()
                    ->required(),
                
                Forms\Components\FileUpload::make('pdf')
                    ->label('PDF')
                    ->columnSpan(2)
                    ->acceptedFileTypes(['application/pdf'])
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => DmSubKegiatan::find($get('sub_kegiatan_id'))->nama . '.pdf')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'kak/' . 
                        Tahun::find($get('tahun_id'))->nama . 
                        '/' . 
                        $get('jenis_kak') . '/' .
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
                // Tables\Columns\TextColumn::make('user.name')
                    // ->label('Nama'),
                Tables\Columns\TextColumn::make('user.subBidang.nama')
                    ->label('Bidang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun.nama')
                    ->label('Tahun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_kegiatan.nama')
                    ->label('Sub Kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kak')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kak usulan' => 'warning',
                        'kak kegiatan' => 'info',
                    }),
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
                    ->fillForm(fn (DuKak $record): array => [
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
                    ->iconButton()
                    ->hidden(fn ($record) => Auth::id() !== $record->user_id)
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
                    ->iconButton()
                    ->hidden(fn ($record) => Auth::id() !== $record->user_id)
                    ->before(function ($record) {
                        if ($record->pdf) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->pdf);
                        }
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDuKaks::route('/'),
        ];
    }
}
