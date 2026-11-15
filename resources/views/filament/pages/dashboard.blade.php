<x-filament-panels::page>
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Total Record</h2>
        <div class="space-y-4">
            @livewire(\App\Filament\Widgets\TotalMangAawitWidget::class)
            @livewire(\App\Filament\Widgets\TotalNagsasanayWidget::class)
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Gawain Attendance</h2>
        <div class="space-y-4">
            @livewire(\App\Filament\Widgets\GawainAttendancePercentageWidget::class)
        </div>
    </div>
</x-filament-panels::page>