<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Nagsasanay</span>
            <x-filament::icon icon="heroicon-o-academic-cap" class="w-5 h-5 text-info-500" />
        </div>
        <p class="text-3xl font-bold mt-1 text-info-600 dark:text-info-400">{{ $total }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Mang-aawit</p>
                <div class="flex gap-2">
                    <x-filament::badge color="info">Male: {{ $mangAawitMale }}</x-filament::badge>
                    <x-filament::badge color="danger">Female: {{ $mangAawitFemale }}</x-filament::badge>
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Organista</p>
                <div class="flex gap-2">
                    <x-filament::badge color="info">Male: {{ $organistaMale }}</x-filament::badge>
                    <x-filament::badge color="danger">Female: {{ $organistaFemale }}</x-filament::badge>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>