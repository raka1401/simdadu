<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuRenaksiResource\Pages;
use App\Filament\Resources\DuRenaksiResource\RelationManagers;
use App\Models\dm_sub_bidang;
use App\Models\DuRenaksi;
use App\Models\Tahun;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use League\CommonMark\Extension\Embed\Embed;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use Spatie\Permission\Models\Role;

class DuRenaksiResource extends Resource
{
    protected static ?string $model = DuRenaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static ?string $navigationGroup = 'Data Dukung';
    protected static ?string $label = 'Rencana Aksi';
    protected static ?string $pluralModelLabel = 'Rencana Aksi';

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
                    ->required()
                    ,
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule 
                            ->where('tahun_id', $get('tahun_id'))
                            ->where('judul', $get('judul'));
                    }, ignoreRecord: true)
                    ->label('Judul Dokumen')
                    ->validationMessages([
                        'unique' => 'Judul Dokumen sudah ada, Silahkan Gunakan Judul Lain.',
                    ])
                    ->live(),
                Forms\Components\FileUpload::make('pdf')
                    ->label('PDF')
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpan(2)
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => $get('judul') . '.pdf')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'renaksi/' . 
                        Tahun::find($get('tahun_id'))->nama . 
                        '/' . 
                        dm_sub_bidang::find($get('sub_bidang_id'))->nama),
                Forms\Components\FileUpload::make('excel')
                    ->label('Excel')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->columnSpan(2)
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => $get('judul') . '.xlsx')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'renaksi/' . 
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
                Group::make('subBidang.dm_bidang.nama')
                    ->label('Bidang')
                    ->collapsible()
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('subBidang.dm_bidang.nama')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun.nama')
                    ->label('Tahun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Nama Dokumen')
                    ->searchable(),
                Tables\Columns\IconColumn::make('pdf')
                    ->default(0)
                    ->boolean()
                    ->url(fn ($record) => $record->pdf ? Storage::url($record->pdf) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\IconColumn::make('excel')
                    ->default(0)
                    ->boolean()
                    ->url(fn ($record) => $record->excel ? Storage::url($record->excel) : null)
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
                    ->fillForm(fn (DuRenaksi $record): array => [
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
                
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDuRenaksis::route('/'),
        ];
    }
}
