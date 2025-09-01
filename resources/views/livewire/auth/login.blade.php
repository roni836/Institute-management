
<div class="font-poppins bg-gray-50">
<div>
        <section class="bg-gray-50">
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                
                <!-- Logo Section -->
                <div class="flex justify-center items-center mb-4">
                    <div class="w-28 h-28 flex items-center justify-center">
                        <img src="{{ asset('logo.png') }}" alt="" class="w-full object-contain">
                    </div>
                </div>

                <div class="w-full bg-white rounded-lg shadow-sm border border-gray-500 md:mt-0 sm:max-w-md xl:p-0">
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        
                        <!-- Header -->
                        <div class="border-b border-gray-200 pb-4">
                            <h1 class="text-2xl font-semibold text-gray-900">
                                Admin Login
                            </h1>
                            <p class="text-gray-600 mt-1">
                                Sign in to access the admin panel
                            </p>
                        </div>

                        <form wire:submit.prevent="login" class="space-y-4 md:space-y-6" action="#">
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Your email</label>
                                <div class="relative">
                                    <input wire:model="email" 
                                           type="email" 
                                           name="email" 
                                           id="email" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white" 
                                           placeholder="ex: Shaique@gmail.com" 
                                           required="">
                                    <svg class="absolute right-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                                <div class="relative">
                                    <input wire:model="password" 
                                           type="password" 
                                           name="password" 
                                           id="password" 
                                           placeholder="••••••••" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white" 
                                           required="">
                                    <svg class="absolute right-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <button type="submit" 
                                    class="w-full px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200 font-medium shadow-sm hover:shadow-md">
                                Sign in
                            </button>

                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-sm font-medium text-gray-600 text-center flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Secure authentication with device registration
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
