<?php

namespace App\Filament\Resources\DuKakResource\Widgets;

use App\Models\dm_sub_bidang;
use App\Models\DmSubKegiatan;
use App\Models\DuKak;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class kakStat extends BaseWidget
{
    protected function getStats(): array
    {
        
        $pdf = DuKak::whereHas('tahun', function ($query) {
            $query->where('nama', '=', date('Y'));
            })->where('pdf', '!=', '0')->count();
        $ygNgumpul = $pdf;
        $subKegiatan = DmSubKegiatan::count();
        // $hitungSubBidang = $subBidang > 0 ? $subBidang * 1 : 0;
        $percentage = $subKegiatan > 0 ? ($ygNgumpul / $subKegiatan) * 100 : 0;
        // $percentage = 42;

        return [
            Stat::make('KAK', $percentage . '%')
            ->description($subKegiatan . ' dari ' . $ygNgumpul . ' yang mengumpulkan KAK')
            ->descriptionIcon($percentage > 50 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
            ->chart([7, 2, 5, 3, 15, 10, 17])
            ->color($percentage > 50 ? 'success' : 'danger'),
        ];
        
    }
}
