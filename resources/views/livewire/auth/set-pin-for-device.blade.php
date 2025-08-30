<!-- resources/views/livewire/auth/set-pin-for-device.blade.php -->
<div class="max-w-md mx-auto py-10">
    <h2 class="text-xl font-semibold mb-4">Set a PIN for this device</h2>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">PIN (4–6 digits)</label>
            <input type="password" inputmode="numeric" pattern="\d*" wire:model.defer="pin" class="w-full border rounded px-3 py-2">
            @error('pin') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Confirm PIN</label>
            <input type="password" inputmode="numeric" pattern="\d*" wire:model.defer="pin_confirmation" class="w-full border rounded px-3 py-2">
            @error('pin_confirmation') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        <button class="w-full bg-black text-white rounded px-4 py-2">Save PIN</button>
    </form>
</div>
