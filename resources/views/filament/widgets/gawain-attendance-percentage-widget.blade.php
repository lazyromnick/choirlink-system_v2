<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <x-filament::section>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Mang-aawit</span>
                <x-filament::icon icon="heroicon-o-musical-note" class="w-5 h-5 text-{{ $this->colorFor($mangAawitPct) }}-500" />
            </div>
            <p class="text-3xl font-bold mt-1 text-{{ $this->colorFor($mangAawitPct) }}-600 dark:text-{{ $this->colorFor($mangAawitPct) }}-400">
                {{ $mangAawitPct }}%
            </p>
            <div class="flex gap-2 mt-3">
                <x-filament::badge color="info">Male: {{ $mangAawitMalePct }}%</x-filament::badge>
                <x-filament::badge color="danger">Female: {{ $mangAawitFemalePct }}%</x-filament::badge>
            </div>
            <p class="text-xs text-gray-400 mt-3">{{ $monthLabel }} · {{ $weekCount }} week(s) recorded</p>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nagsasanay</span>
                <x-filament::icon icon="heroicon-o-academic-cap" class="w-5 h-5 text-{{ $this->colorFor($nagsasanayPct) }}-500" />
            </div>
            <p class="text-3xl font-bold mt-1 text-{{ $this->colorFor($nagsasanayPct) }}-600 dark:text-{{ $this->colorFor($nagsasanayPct) }}-400">
                {{ $nagsasanayPct }}%
            </p>
            <p class="text-xs text-gray-400 mt-3">{{ $monthLabel }} · {{ $weekCount }} week(s) recorded</p>
        </x-filament::section>

    </div>
</x-filament-widgets::widget>