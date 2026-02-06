<x-layouts::auth.simple :title="__('Register')">
    <div class="flex flex-col gap-2 text-center">
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ __('CrÃ©er un compte') }}</h1>
        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('CrÃ©ez votre accÃ¨s en quelques secondes') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Nom complet') }}</label>
            <input name="name" type="text" value="{{ old('name') }}" required autofocus
                class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900 dark:text-white" />
            @error('name') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Email') }}</label>
            <input name="email" type="email" value="{{ old('email') }}" required
                class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900 dark:text-white" />
            @error('email') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Mot de passe') }}</label>
            <input name="password" type="password" required
                class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900 dark:text-white" />
            @error('password') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Confirmer le mot de passe') }}</label>
            <input name="password_confirmation" type="password" required
                class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900 dark:text-white" />
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-800 dark:bg-white dark:text-neutral-900">
            {{ __('CrÃ©er le compte') }}
        </button>

        <p class="text-center text-sm text-neutral-600 dark:text-neutral-400">
            {{ __('DÃ©jÃ  un compte ?') }}
            <a href="{{ route('login') }}" class="text-neutral-800 underline dark:text-neutral-200">{{ __('Se connecter') }}</a>
        </p>
    </form>
</x-layouts::auth.simple>
