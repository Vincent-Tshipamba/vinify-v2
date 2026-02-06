<x-layouts::auth.simple :title="__('Log in')">
    <div class="flex flex-col gap-2 text-center">
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ __('Se connecter') }}</h1>
        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('AccÃ©dez Ã  votre compte') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Email') }}</label>
            <input name="email" type="email" value="{{ old('email') }}" required autofocus
                class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900 dark:text-white" />
            @error('email') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Mot de passe') }}</label>
            <input name="password" type="password" required
                class="mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-400 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900 dark:text-white" />
            @error('password') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                <input type="checkbox" name="remember" class="rounded border-neutral-300 dark:border-neutral-700" />
                {{ __('Se souvenir de moi') }}
            </label>
            <a href="{{ route('register') }}" class="text-neutral-800 underline dark:text-neutral-200">
                {{ __('CrÃ©er un compte') }}
            </a>
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-800 dark:bg-white dark:text-neutral-900">
            {{ __('Connexion') }}
        </button>
    </form>
</x-layouts::auth.simple>
