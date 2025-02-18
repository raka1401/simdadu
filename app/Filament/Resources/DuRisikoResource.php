<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuRisikoResource\Pages;
use App\Filament\Resources\DuRisikoResource\RelationManagers;
use App\Models\dm_sub_bidang;
use App\Models\DuRisiko;
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

class DuRisikoResource extends Resource
{
    protected static ?string $modelLabel = 'Manajemen Risiko';
    protected static ?string $model = DuRisiko::class;
    protected static ?string $label = 'Manajemen Risiko';
    protected static ?string $pluralModelLabel = 'Manajemen Risiko';
    protected static ?string $navigationGroup = 'Data Dukung';

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

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
                    ->label('Tahun')
                    ->live()
                    ->required(),
                Forms\Components\Select::make('kategori')
                    ->options([
                        'Register Risiko' => 'Register Risiko',
                        'Fraud Risiko' => 'Fraud Risiko',
                    ])
                    ->live(),
                Forms\Components\FileUpload::make('pdf')
                    ->label('PDF')
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpan(2)
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => $get('kategori') . '.pdf')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'risiko/' . 
                        Tahun::find($get('tahun_id'))->nama . 
                        '/' . 
                        dm_sub_bidang::find($get('sub_bidang_id'))->nama),
                Forms\Components\FileUpload::make('excel')
                    ->label('Excel')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->columnSpan(2)
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => $get('kategori') . '.xlsx')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'risiko/' . 
                        Tahun::find($get('tahun_id'))->nama . 
                        '/' . 
                        dm_sub_bidang::find($get('sub_bidang_id'))->nama),
            ]);
    } 

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('tahun_id', session('selected_tahun'));
            })
            ->emptyStateHeading('Belum Ada Data')
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
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Register Risiko' => 'primary',
                        'Fraud Risiko' => 'warning',
                    }),
                Tables\Columns\IconColumn::make('pdf')
                    ->boolean()
                    ->default(0)
                    ->url(fn ($record) => $record->pdf ? Storage::url($record->pdf) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\IconColumn::make('excel')
                    ->boolean()
                    ->default(0)
                    ->url(fn ($record) => $record->excel ? Storage::url($record->excel) : null)
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
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Verifikasi')
                    ->iconButton()
                    ->color('success')
                    ->icon('heroicon-o-paper-clip')
                    ->hidden(fn ($record) => !Auth::user()->hasRole(['super_admin','admin','verifikator']))
                    ->fillForm(fn (DuRisiko $record): array => [
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
                    ->hidden(fn ($record) => !Auth::user()->hasRole(['super_admin','admin']) && Auth::id() !== $record->user_id)
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
                    ->hidden(fn ($record) => !Auth::user()->hasRole(['super_admin','admin']) && Auth::id() !== $record->user_id)
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDuRisikos::route('/'),
        ];
    }
}
