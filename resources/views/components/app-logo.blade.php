@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="{{ env('APP_NAME') }}" {{ $attributes }}>
        <x-slot name="logo" class="flex justify-center items-center rounded-md size-8 aspect-square bg-accent-content text-accent-foreground">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ env('APP_NAME') }}" {{ $attributes }}>
        <x-slot name="logo" class="flex justify-center items-center rounded-md size-8 aspect-square bg-accent-content text-accent-foreground">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:brand>
@endif
