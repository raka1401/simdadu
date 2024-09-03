<?php

namespace App\Filament\Resources\DuRenaksiResource\Widgets;

use App\Models\dm_bidang;
use App\Models\dm_sub_bidang;
use App\Models\DmSubKegiatan;
use App\Models\DuKak;
use App\Models\DuRenaksi;
use App\Models\Tahun;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RenaksiStat extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        // GLOBAL
        $subBidang = dm_sub_bidang::count();
        $subKegiatan = DmSubKegiatan::count();
        $user = User::count();


        // RENAKSI
        $pdfrenaksi = DuRenaksi::whereHas('tahun', function ($query) {
            $query->where('nama', '=', date('Y'));
        })->where('pdf', '!=', '0')->count();
        $exrenaksi = DuRenaksi::whereHas('tahun', function ($query) {
            $query->where('nama', '=', date('Y'));
        })->where('excel', '!=', '0')->count();
        $kumpulrenaksi = $pdfrenaksi + $exrenaksi;
        $totalrenaksi = $subBidang > 0 ? $subBidang * 2 : 0;
        $hitungrenaksi = $subBidang > 0 ? ($kumpulrenaksi / $totalrenaksi) * 100 : 0;

        // KAK
        $pdfkak = DuKak::whereHas('tahun', function ($query) {
            $query->where('nama', '=', date('Y'));
            })->where('pdf', '!=', '0')->count();
        $kumpulkak = $pdfkak;
        $hitungkak = $subKegiatan > 0 ? ($kumpulkak / $subKegiatan) * 100 : 0;

        // IKI
        $pdfiki = DuRenaksi::whereHas('tahun', function ($query) {
            $query->where('nama', '=', date('Y'));
        })->where('pdf', '!=', '0')->count();
        $kumpuliki = $pdfiki;
        $hitungiki = $user > 0 ? ($kumpuliki / $user) * 100 : 0;

        // EVALIN
        $pdfevalin = DuRenaksi::whereHas('tahun', function ($query) {
            $query->where('nama', '=', date('Y'));
        })->where('pdf', '!=', '0')->count();
        $exevalin = DuRenaksi::whereHas('tahun', function ($query) {
            $query->where('nama', '=', date('Y'));
        })->where('excel', '!=', '0')->count();
        $kumpulevalin = $pdfevalin + $exevalin;
        $hitungevalin = $subKegiatan > 0 ? ($kumpulevalin / $subKegiatan) * 100 : 0;

        return [
            // RENAKSI STAT
            Stat::make('Rencana Aksi', $hitungrenaksi . '%')
            ->description($totalrenaksi . ' dari ' . $kumpulrenaksi . ' yang mengumpulkan rencana aksi (PDF/EXCEL)')
            ->descriptionIcon($hitungrenaksi > 50 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color($hitungrenaksi > 50 ? 'success' : 'danger'),

            // KAK STAT
            Stat::make('KAK', $hitungkak . '%')
            ->description($subKegiatan . ' dari ' . $kumpulkak . ' yang mengumpulkan KAK')
            ->descriptionIcon($hitungkak > 50 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
            ->chart([7, 2, 5, 3, 15, 10, 17])
            ->color($hitungkak > 50 ? 'success' : 'danger'),

            // IKI STAT
            Stat::make('IKI', $hitungiki . '%')
            ->description($user . ' dari ' . $kumpuliki . ' yang mengumpulkan IKI')
            ->descriptionIcon($hitungiki > 50 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
            ->chart([7, 2, 3, 4, 10, 20, 17])
            ->color($hitungiki > 50 ? 'success' : 'danger'),

        ];

        

        
        
    } 
}
