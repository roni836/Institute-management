    <div class="container mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-md p-8 max-w-lg mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">
                Cancel Subscription
            </h1>

            <p class="text-gray-600 text-center mb-6">
                Please let us know why you are cancelling. Your feedback helps us improve.
            </p>

            <form wire:submit.prevent="save"  class="space-y-4">
                @csrf
                
                <!-- Reason Field -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for cancellation
                    </label>
                    <textarea  wire:model="cancel_reason" name="reason" rows="4"
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Type your reason here..." required></textarea>
                    @error('reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-between">
                    <a href="{{ url()->previous() }}">
                        <button type="button"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-xl shadow">
                            Go Back
                        </button>
                    </a>

                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl shadow">
                        Cancel Admission
                    </button>
                </div>
            </form>
        </div>
    </div>
