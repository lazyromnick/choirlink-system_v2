<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <x-filament::section>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Mang-aawit</span>
                <x-filament::icon icon="heroicon-o-users" class="w-5 h-5 text-primary-500" />
            </div>
            <p class="text-3xl font-bold mt-1">{{ $total }}</p>
            <div class="flex gap-2 mt-3">
                <x-filament::badge color="info">Male: {{ $totalMale }}</x-filament::badge>
                <x-filament::badge color="danger">Female: {{ $totalFemale }}</x-filament::badge>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Mang-aawit</span>
                <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 text-success-500" />
            </div>
            <p class="text-3xl font-bold mt-1 text-success-600 dark:text-success-400">{{ $active }}</p>
            <div class="flex gap-2 mt-3">
                <x-filament::badge color="info">Male: {{ $activeMale }}</x-filament::badge>
                <x-filament::badge color="danger">Female: {{ $activeFemale }}</x-filament::badge>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Inactive Mang-aawit</span>
                <x-filament::icon icon="heroicon-o-x-circle" class="w-5 h-5 text-danger-500" />
            </div>
            <p class="text-3xl font-bold mt-1 text-danger-600 dark:text-danger-400">{{ $inactive }}</p>
            <div class="flex gap-2 mt-3">
                <x-filament::badge color="info">Male: {{ $inactiveMale }}</x-filament::badge>
                <x-filament::badge color="danger">Female: {{ $inactiveFemale }}</x-filament::badge>
            </div>
        </x-filament::section>

    </div>
</x-filament-widgets::widget>