<!-- resources/views/livewire/auth/admin-pin-login.blade.php -->
<div class="max-w-md mx-auto py-10">
    <h2 class="text-xl font-semibold mb-4">Enter PIN</h2>
    <form wire:submit.prevent="loginWithPin" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">PIN</label>
            <input type="password" inputmode="numeric" pattern="\d*" wire:model.defer="pin" class="w-full border rounded px-3 py-2">
            @error('pin') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        <button class="w-full bg-black text-white rounded px-4 py-2">Unlock</button>
    </form>

    <button wire:click="logoutDevice" class="mt-4 text-sm text-gray-600 underline">
        Not your device? Use password instead
    </button>
</div>
