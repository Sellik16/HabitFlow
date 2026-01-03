<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-lg font-semibold mb-4">
        Zaloguj się kodem z emaila
    </h1>

    <form method="POST" action="{{ route('auth.otp.request') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Wyślij kod') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-sm text-gray-600">
        <a class="underline" href="{{ route('login') }}">
            Wróć do logowania hasłem
        </a>
    </div>
</x-guest-layout>
