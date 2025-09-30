<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-white dark:text-gray-100 mb-2">Welcome Back</h1>
        <p class="text-white/80 dark:text-gray-300">Sign in to your account to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-white/60 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
                <x-text-input id="email" class="pl-10" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your email" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-white/60 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <x-text-input id="password" class="pl-10" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Login As Client -->
        <div class="space-y-4">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-white/20 rounded bg-white/10" name="remember">
                <label for="remember_me" class="ml-3 text-sm text-white/90 dark:text-gray-300">
                    {{ __('Remember me') }}
                </label>
            </div>
            
            <div class="flex items-center">
                <input id="login_as_client" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-white/20 rounded bg-white/10" name="login_as_client">
                <label for="login_as_client" class="ml-3 text-sm text-white/90 dark:text-gray-300">
                    {{ __('Login As Client') }}
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <x-primary-button class="w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                {{ __('Sign In') }}
            </x-primary-button>
        </div>

        <!-- Forgot Password Link -->
        <div class="text-center">
            @if (Route::has('password.request'))
                <a class="text-sm text-white/80 dark:text-gray-300 hover:text-white dark:hover:text-gray-100 transition-colors duration-200 underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Register Link -->
        @if (Route::has('register'))
            <div class="text-center pt-4 border-t border-white/20 dark:border-gray-600">
                <p class="text-sm text-white/80 dark:text-gray-300">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-semibold text-white dark:text-gray-100 hover:text-primary-300 dark:hover:text-primary-400 transition-colors duration-200">
                        {{ __('Sign up') }}
                    </a>
                </p>
            </div>
        @endif
    </form>
</x-guest-layout>