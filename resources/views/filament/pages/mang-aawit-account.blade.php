<x-filament-panels::page>
    <div class="flex justify-end mb-4">
        <x-filament::button
            wire:click="toggleArchived"
            color="gray"
            icon="heroicon-o-archive-box"
        >
            {{ $showArchived ? 'View Active' : 'View Archived' }}
        </x-filament::button>
    </div>

    {{ $this->table }}
</x-filament-panels::page>