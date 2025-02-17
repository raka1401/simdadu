@php
    $tahun = \App\Models\Tahun::find(session('selected_tahun'));
@endphp

<div class="flex items-center gap-2 px-4">
    <x-heroicon-o-calendar class="w-5 h-5 text-warning-500" />
    <span class="text-sm font-medium text-warning-500">
        Tahun: {{ $tahun ? $tahun->nama : '-' }}
    </span>
</div>
