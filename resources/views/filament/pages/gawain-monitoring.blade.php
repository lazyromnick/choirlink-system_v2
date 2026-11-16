<x-filament-panels::page>
    {{-- Month/Year Selector --}}
    <div class="flex items-center gap-4 mb-6">
        <x-filament::button
            wire:click="previousMonth"
            color="gray"
            icon="heroicon-o-chevron-left"
            size="sm"
        />
        <span class="text-lg font-semibold px-4">
            {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
        </span>
        <x-filament::button
            wire:click="nextMonth"
            color="gray"
            icon="heroicon-o-chevron-right"
            size="sm"
        />
        <x-filament::button
            wire:click="goToCurrentMonth"
            color="gray"
            size="sm"
        >
            Current Month
        </x-filament::button>
    </div>

    {{-- Mang-aawit Section --}}
    <x-filament::section>
        <x-slot name="heading">Mang-aawit</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-3 w-32">ID</th>
                        <th class="text-left py-2 px-3 w-48">Name</th>
                        @foreach($weeks as $week)
                            <th class="text-center py-2 px-2 min-w-[90px]">
                                <div class="text-xs font-semibold">Week {{ $week->week_number }}</div>
                                <div class="text-xs text-gray-500">{{ $week->start_date->format('M d') }} - {{ $week->end_date->format('d') }}</div>
                            </th>
                        @endforeach
                        <th class="text-center py-2 px-3 font-bold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mangAawitList as $mangAawit)
                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-2 px-3 text-xs text-gray-500">{{ $mangAawit->mang_aawit_id }}</td>
                            <td class="py-2 px-3 font-medium">{{ $mangAawit->full_name }}</td>
                            @foreach($weeks as $week)
                                <td class="text-center py-2 px-2">
                                    <input
                                        type="checkbox"
                                        wire:model="pendingMangAawit.{{ $mangAawit->id }}.{{ $week->id }}"
                                        class="rounded border-gray-300 text-primary-600 cursor-pointer w-4 h-4"
                                    />
                                </td>
                            @endforeach
                            <td class="text-center py-2 px-3 font-bold text-primary-600">
                                {{ $this->getMangAawitTotal($mangAawit->id) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($weeks) + 3 }}" class="text-center py-4 text-gray-400">
                                No active Mang-aawit found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- Nagsasanay Section --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Nagsasanay</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-3 w-32">ID</th>
                        <th class="text-left py-2 px-3 w-48">Name</th>
                        <th class="text-left py-2 px-3 w-28">Track</th>
                        @foreach($weeks as $week)
                            <th class="text-center py-2 px-2 min-w-[90px]">
                                <div class="text-xs font-semibold">Week {{ $week->week_number }}</div>
                                <div class="text-xs text-gray-500">{{ $week->start_date->format('M d') }} - {{ $week->end_date->format('d') }}</div>
                            </th>
                        @endforeach
                        <th class="text-center py-2 px-3 font-bold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nagsasanayList as $nagsasanay)
                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-2 px-3 text-xs text-gray-500">{{ $nagsasanay->mang_aawit_id }}</td>
                            <td class="py-2 px-3 font-medium">{{ $nagsasanay->full_name }}</td>
                            <td class="py-2 px-3 text-xs">{{ $nagsasanay->training_track }}</td>
                            @foreach($weeks as $week)
                                <td class="text-center py-2 px-2">
                                    <input
                                        type="checkbox"
                                        wire:model="pendingNagsasanay.{{ $nagsasanay->id }}.{{ $week->id }}"
                                        class="rounded border-gray-300 text-primary-600 cursor-pointer w-4 h-4"
                                    />
                                </td>
                            @endforeach
                            <td class="text-center py-2 px-3 font-bold text-primary-600">
                                {{ $this->getNagsasanayTotal($nagsasanay->id) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($weeks) + 4 }}" class="text-center py-4 text-gray-400">
                                No active Nagsasanay found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <div class="flex justify-end mt-4 mb-6">
        <x-filament::button
            wire:click="recordAttendance"
            wire:loading.attr="disabled"
            wire:target="recordAttendance"
            color="success"
            icon="heroicon-o-check-circle"
            size="lg"
        >
            <span wire:loading.remove wire:target="recordAttendance">Record Attendance</span>
            <span wire:loading wire:target="recordAttendance">Saving...</span>
        </x-filament::button>
    </div>

    {{-- Summary Section --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Summary — {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-sm mb-3 text-gray-600 dark:text-gray-400">Mang-aawit</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Name</th>
                            <th class="text-center py-2">Weeks</th>
                            <th class="text-center py-2">Present</th>
                            <th class="text-center py-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mangAawitList as $mangAawit)
                            @php $total = $this->getMangAawitTotal($mangAawit->id); @endphp
                            <tr class="border-b">
                                <td class="py-2">{{ $mangAawit->full_name }}</td>
                                <td class="text-center py-2">{{ count($weeks) }}</td>
                                <td class="text-center py-2">{{ $total }}</td>
                                <td class="text-center py-2">{{ count($weeks) > 0 ? round(($total / count($weeks)) * 100) : 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <h3 class="font-semibold text-sm mb-3 text-gray-600 dark:text-gray-400">Nagsasanay</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Name</th>
                            <th class="text-center py-2">Weeks</th>
                            <th class="text-center py-2">Present</th>
                            <th class="text-center py-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nagsasanayList as $nagsasanay)
                            @php $total = $this->getNagsasanayTotal($nagsasanay->id); @endphp
                            <tr class="border-b">
                                <td class="py-2">{{ $nagsasanay->full_name }}</td>
                                <td class="text-center py-2">{{ count($weeks) }}</td>
                                <td class="text-center py-2">{{ $total }}</td>
                                <td class="text-center py-2">{{ count($weeks) > 0 ? round(($total / count($weeks)) * 100) : 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>