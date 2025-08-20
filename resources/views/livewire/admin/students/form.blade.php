<div :title="$this->student ? 'Edit Student' : 'New Student'">
    @if (session('ok'))
      <div class="mb-3 p-2 rounded bg-green-50 border text-green-800">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="save" class="grid md:grid-cols-2 gap-4">
        <div class="bg-white border rounded-xl p-4 space-y-3">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs">First Name</label>
                    <input class="w-full border rounded p-2" wire:model="first_name">
                    @error('first_name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs">Last Name</label>
                    <input class="w-full border rounded p-2" wire:model="last_name">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs">Email</label>
                    <input class="w-full border rounded p-2" wire:model="email" type="email">
                    @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs">Phone</label>
                    <input class="w-full border rounded p-2" wire:model="phone">
                    @error('phone')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="text-xs">DOB</label>
                    <input type="date" class="w-full border rounded p-2" wire:model="dob">
                </div>
                <div>
                    <label class="text-xs">Gender</label>
                    <select class="w-full border rounded p-2" wire:model="gender">
                        <option value="">—</option>
                        <option value="male">Male</option><option value="female">Female</option><option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs">Status</label>
                    <select class="w-full border rounded p-2" wire:model="status">
                        <option value="active">Active</option><option value="inactive">Inactive</option><option value="alumni">Alumni</option>
                    </select>
                    @error('status')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="text-xs">Address</label>
                <textarea class="w-full border rounded p-2" rows="3" wire:model="address"></textarea>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Guardian</h3>
            <div>
                <label class="text-xs">Name</label>
                <input class="w-full border rounded p-2" wire:model="g_name">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs">Relation</label>
                    <input class="w-full border rounded p-2" wire:model="g_relation">
                </div>
                <div>
                    <label class="text-xs">Phone</label>
                    <input class="w-full border rounded p-2" wire:model="g_phone">
                </div>
            </div>
            <div>
                <label class="text-xs">Email</label>
                <input class="w-full border rounded p-2" wire:model="g_email" type="email">
            </div>

            <div class="pt-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-black text-white">Save</button>
                <a href="{{ route('admin.students.index') }}" class="ml-2 px-4 py-2 rounded-lg border">Cancel</a>
            </div>
        </div>
    </form>
</div>
