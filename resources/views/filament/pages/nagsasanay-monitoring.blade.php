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

    {{-- Add Date Form --}}
    <x-filament::section>
        <x-slot name="heading">Add Session Date</x-slot>
        <div class="flex items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session Date</label>
                <input
                    type="date"
                    wire:model="newSessionDate"
                    class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:text-white"
                />
            </div>
            <x-filament::button
                wire:click="saveSession"
                color="success"
                icon="heroicon-o-plus"
                size="sm"
            >
                Add Date
            </x-filament::button>
        </div>
    </x-filament::section>

    {{-- Mang-aawit Section --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Nagsasanay — Mang-aawit</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-3 w-32">ID</th>
                        <th class="text-left py-2 px-3 w-48">Name</th>
                        @foreach($sessions as $session)
                            <th class="text-center py-2 px-2 min-w-[80px]">
                                <div class="text-xs font-medium">{{ $session->session_date->format('M d') }}</div>
                                <button wire:click="removeSession({{ $session->id }})" class="text-red-400 hover:text-red-600 text-xs mt-1">✕</button>
                            </th>
                        @endforeach
                        <th class="text-center py-2 px-3 font-bold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mangAawitList as $nagsasanay)
                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-2 px-3 text-xs text-gray-500">{{ $nagsasanay->mang_aawit_id }}</td>
                            <td class="py-2 px-3 font-medium">{{ $nagsasanay->full_name }}</td>
                            @foreach($sessions as $session)
                                <td class="text-center py-2 px-2">
                                    <input
                                        type="checkbox"
                                        wire:model="pending.{{ $nagsasanay->id }}.{{ $session->id }}"
                                        class="rounded border-gray-300 text-primary-600 cursor-pointer w-4 h-4"
                                    />
                                </td>
                            @endforeach
                            <td class="text-center py-2 px-3 font-bold text-primary-600">
                                {{ $this->getTotal($nagsasanay->id) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($sessions) + 3 }}" class="text-center py-4 text-gray-400">
                                No active Nagsasanay (Mang-aawit) found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- Organista Section --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Nagsasanay — Organista</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-3 w-32">ID</th>
                        <th class="text-left py-2 px-3 w-48">Name</th>
                        @foreach($sessions as $session)
                            <th class="text-center py-2 px-2 min-w-[80px]">
                                <div class="text-xs font-medium">{{ $session->session_date->format('M d') }}</div>
                            </th>
                        @endforeach
                        <th class="text-center py-2 px-3 font-bold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organistaList as $nagsasanay)
                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-2 px-3 text-xs text-gray-500">{{ $nagsasanay->mang_aawit_id }}</td>
                            <td class="py-2 px-3 font-medium">{{ $nagsasanay->full_name }}</td>
                            @foreach($sessions as $session)
                                <td class="text-center py-2 px-2">
                                    <input
                                        type="checkbox"
                                        wire:model="pending.{{ $nagsasanay->id }}.{{ $session->id }}"
                                        class="rounded border-gray-300 text-primary-600 cursor-pointer w-4 h-4"
                                    />
                                </td>
                            @endforeach
                            <td class="text-center py-2 px-3 font-bold text-primary-600">
                                {{ $this->getTotal($nagsasanay->id) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($sessions) + 3 }}" class="text-center py-4 text-gray-400">
                                No active Nagsasanay (Organista) found.
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
                            <th class="text-center py-2">Sessions</th>
                            <th class="text-center py-2">Attended</th>
                            <th class="text-center py-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mangAawitList as $nagsasanay)
                            @php $total = $this->getTotal($nagsasanay->id); @endphp
                            <tr class="border-b">
                                <td class="py-2">{{ $nagsasanay->full_name }}</td>
                                <td class="text-center py-2">{{ count($sessions) }}</td>
                                <td class="text-center py-2">{{ $total }}</td>
                                <td class="text-center py-2">{{ count($sessions) > 0 ? round(($total / count($sessions)) * 100) : 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <h3 class="font-semibold text-sm mb-3 text-gray-600 dark:text-gray-400">Organista</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Name</th>
                            <th class="text-center py-2">Sessions</th>
                            <th class="text-center py-2">Attended</th>
                            <th class="text-center py-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($organistaList as $nagsasanay)
                            @php $total = $this->getTotal($nagsasanay->id); @endphp
                            <tr class="border-b">
                                <td class="py-2">{{ $nagsasanay->full_name }}</td>
                                <td class="text-center py-2">{{ count($sessions) }}</td>
                                <td class="text-center py-2">{{ $total }}</td>
                                <td class="text-center py-2">{{ count($sessions) > 0 ? round(($total / count($sessions)) * 100) : 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>