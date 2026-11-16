<x-filament-panels::page>
    {{-- Month/Year Selector --}}
    <div class="flex items-center gap-4 mb-6">
        <x-filament::button wire:click="previousMonth" color="gray" icon="heroicon-o-chevron-left" size="sm" />
        <span class="text-lg font-semibold px-4">
            {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
        </span>
        <x-filament::button wire:click="nextMonth" color="gray" icon="heroicon-o-chevron-right" size="sm" />
        <x-filament::button wire:click="goToCurrentMonth" color="gray" size="sm">
            Current Month
        </x-filament::button>
    </div>

    <x-filament::section>
        <x-slot name="heading">Weekly Handugan Participation</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-3">Week</th>
                        <th class="text-left py-2 px-3">Denominator</th>
                        <th class="text-center py-2 px-3">Tanging Handugan</th>
                        <th class="text-center py-2 px-3">TH %</th>
                        <th class="text-center py-2 px-3">Lingap</th>
                        <th class="text-center py-2 px-3">Lingap %</th>
                        <th class="text-center py-2 px-3">Lagak</th>
                        <th class="text-center py-2 px-3">Lagak %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weeks as $week)
                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-2 px-3">
                                <div class="font-semibold">Week {{ $week->week_number }}</div>
                                <div class="text-xs text-gray-500">{{ $week->start_date->format('M d') }} - {{ $week->end_date->format('d') }}</div>
                            </td>
                            <td class="py-2 px-3 text-gray-500">{{ $week->denominator_snapshot }}</td>
                            <td class="text-center py-2 px-2">
                                <input type="number" min="0"
                                    wire:model="pending.{{ $week->id }}.th_count"
                                    class="w-20 text-center border border-gray-300 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 dark:text-white" />
                            </td>
                            <td class="text-center py-2 px-3 font-semibold">{{ $week->thPercentage() }}%</td>
                            <td class="text-center py-2 px-2">
                                <input type="number" min="0"
                                    wire:model="pending.{{ $week->id }}.lingap_count"
                                    class="w-20 text-center border border-gray-300 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 dark:text-white" />
                            </td>
                            <td class="text-center py-2 px-3 font-semibold">{{ $week->lingapPercentage() }}%</td>
                            <td class="text-center py-2 px-2">
                                <input type="number" min="0"
                                    wire:model="pending.{{ $week->id }}.lagak_count"
                                    class="w-20 text-center border border-gray-300 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 dark:text-white" />
                            </td>
                            <td class="text-center py-2 px-3 font-semibold">{{ $week->lagakPercentage() }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-400">No weeks found for this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <div class="flex justify-end mt-4 mb-6">
        <x-filament::button
            wire:click="recordHandugan"
            wire:loading.attr="disabled"
            wire:target="recordHandugan"
            color="success"
            icon="heroicon-o-check-circle"
            size="lg"
        >
            <span wire:loading.remove wire:target="recordHandugan">Record Handugan</span>
            <span wire:loading wire:target="recordHandugan">Saving...</span>
        </x-filament::button>
    </div>

    <x-filament::section>
        <x-slot name="heading">Monthly Summary — {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanging Handugan</p>
                <p class="text-3xl font-bold mt-1">{{ $monthlyAverages['th'] }}%</p>
            </div>
            <div class="text-center">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lingap</p>
                <p class="text-3xl font-bold mt-1">{{ $monthlyAverages['lingap'] }}%</p>
            </div>
            <div class="text-center">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lagak</p>
                <p class="text-3xl font-bold mt-1">{{ $monthlyAverages['lagak'] }}%</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>