<div class="max-w-xl mx-auto p-6 space-y-6">
    <h1 class="text-xl font-semibold">Add Teacher</h1>

    @if (session('success'))
        <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="p-3 rounded bg-red-100 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" wire:model.defer="name" class="border rounded px-3 py-2 w-full">
            @error('name') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" wire:model.defer="email" class="border rounded px-3 py-2 w-full">
            @error('email') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="tel" wire:model.defer="phone" 
                   class="border rounded px-3 py-2 w-full"
                   placeholder="Enter phone number">
            @error('phone') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Address</label>
            <textarea wire:model.defer="address" 
                      class="border rounded px-3 py-2 w-full"
                      rows="3"
                      placeholder="Enter full address"></textarea>
            @error('address') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Expertise</label>
            <input type="text" wire:model.defer="expertise" 
                   class="border rounded px-3 py-2 w-full"
                   placeholder="e.g. Mathematics, Physics, Computer Science">
            @error('expertise') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div class="border rounded p-4 bg-gray-50">
            <div class="flex items-center gap-2 mb-2">
                <input id="apw" type="checkbox" wire:model.live="autoPassword" class="rounded">
                <label for="apw" class="text-sm font-medium">Auto-generate password</label>
            </div>
            <p class="text-xs text-gray-600">
                @if($autoPassword)
                    ✓ A secure password will be automatically generated and sent to the teacher's email address.
                @else
                    ✓ You can manually set a password for the teacher account.
                @endif
            </p>
        </div>

        @unless($autoPassword)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" wire:model.defer="password" class="border rounded px-3 py-2 w-full">
                    @error('password') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input type="password" wire:model.defer="password_confirmation" class="border rounded px-3 py-2 w-full">
                </div>
            </div>
        @endunless

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span wire:loading.remove>Save Teacher</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </form>
</div>
