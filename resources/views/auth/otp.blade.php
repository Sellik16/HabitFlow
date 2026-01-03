<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('auth.otp.verify') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                value="{{ old('email', session('otp_email')) }}"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="code" :value="__('Kod (6 cyfr)')" />
            <x-text-input
                id="code"
                class="block mt-1 w-full"
                type="text"
                name="code"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        @if(!empty($expiresAtIso))
            <div class="mb-4 text-sm text-gray-600">
                Kod wygaśnie za: <span id="otp-timer" class="font-semibold">--:--</span>
            </div>
        @else
            <div class="mb-4 text-sm text-gray-600">
                Kod jest aktywny przez ograniczony czas. Jeśli nie działa, wyślij nowy.
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Zaloguj') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-sm text-gray-600">
        Nie przyszedł kod?
        <a class="underline" href="{{ route('login') }}">Wróć i wyślij ponownie</a>.
    </div>

@if(!empty($expiresAtIso))
    <script>
        (function () {
            const expiresAt = new Date(@json($expiresAtIso)).getTime();
            const el = document.getElementById('otp-timer');

            function pad(n) { return String(n).padStart(2, '0'); }

            function tick() {
                const now = Date.now();
                let diffMs = expiresAt - now;

                if (diffMs <= 0) {
                    el.textContent = '00:00';
                    el.classList.add('text-red-600');

                    // opcjonalnie: zablokuj inputy po wygaśnięciu
                    // document.querySelector('button[type="submit"]').disabled = true;

                    return;
                }

                const diffSec = Math.floor(diffMs / 1000);
                const m = Math.floor(diffSec / 60);
                const s = diffSec % 60;

                el.textContent = `${pad(m)}:${pad(s)}`;
                requestAnimationFrame(() => {}); // no-op, tylko żeby nie “zawieszało” w niektórych środowiskach
                setTimeout(tick, 500);
            }

            if (el) tick();
        })();
    </script>
@endif
</x-guest-layout>
