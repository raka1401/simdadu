<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuEvalinResource\Pages;
use App\Filament\Resources\DuEvalinResource\RelationManagers;
use App\Models\dm_bidang;
use App\Models\dm_sub_bidang;
use App\Models\DmSubKegiatan;
use App\Models\DuEvalin;
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

class DuEvalinResource extends Resource
{
    protected static ?string $model = DuEvalin::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationGroup = 'Data Dukung';
    protected static ?string $modelLabel = 'Evaluasi Kinerja Internal';
    protected static ?string $pluralModelLabel = 'Evaluasi Kinerja Internal';

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
                Forms\Components\Select::make('triwulan')
                    ->options([
                        'TW I' => 'Triwulan I',
                        'TW II' => 'Triwulan II',
                        'TW III' => 'Triwulan III',
                        'TW IV' => 'Triwulan IV',
                    ])
                    ->live()
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule 
                            ->where('tahun_id', $get('tahun_id'))
                            ->where('user_id', $get('user_id'))
                            ->where ('triwulan', $get('triwulan'));
                    }, ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Evalin anda pada triwulan itu sudah ada sebelumnya.',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('pdf')
                    ->nullable()
                    ->columnSpan(2)
                    ->acceptedFileTypes(['application/pdf'])
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => Auth::user()->name . '.pdf')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'evalin/' . 
                        Tahun::find($get('tahun_id'))->nama . 
                        '/' . $get('triwulan') . 
                        '/' . dm_bidang::find(dm_sub_bidang::find($get('sub_bidang_id'))->dm_bidang_id)->nama.
                        '/' . dm_sub_bidang::find($get('sub_bidang_id'))->nama),
                Forms\Components\FileUpload::make('excel')
                    ->nullable()
                    ->columnSpan(2)
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->getUploadedFileNameForStorageUsing(fn (Get $get): string => Auth::user()->name . '.xlsx')
                    ->disk('public')
                    ->directory(fn (Get $get): string => 
                        'evalin/' . 
                        Tahun::find($get('tahun_id'))->nama . 
                        '/' . $get('triwulan') . 
                        '/' . dm_bidang::find(dm_sub_bidang::find($get('sub_bidang_id'))->dm_bidang_id)->nama .
                        '/' . dm_sub_bidang::find($get('sub_bidang_id'))->nama),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('tahun_id', session('selected_tahun'));
            })
            ->emptyStateHeading('Tidak Ada Data')
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
                Tables\Columns\TextColumn::make('triwulan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TW I' => 'primary',
                        'TW II' => 'warning',
                        'TW III' => 'success',
                        'TW IV' => 'info',
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
                    ->fillForm(fn (DuEvalin $record): array => [
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
            'index' => Pages\ManageDuEvalins::route('/'),
        ];
    }
}
