<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <form wire:submit="create">
            {{ $this->form }}

            <div style="padding-top: 20px;">
                <button type="submit" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                    Submit
                </button>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>