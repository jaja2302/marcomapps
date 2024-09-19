<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <form wire:submit="create">
            {{ $this->form }}

            <button type="submit">
                Submit
            </button>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>