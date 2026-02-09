<x-layouts::auth.simple :title="__('Log in')">
    <div class="flex flex-col gap-2 text-center">
        <h1 class="font-semibold text-neutral-900 dark:text-white text-2xl">{{ __('Se connecter') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 text-sm">{{ __('Accédez à votre compte') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="text-neutral-700 dark:text-neutral-300 text-sm">{{ __('Email') }}</label>
            <input name="email" type="email" value="{{ old('email') }}" required autofocus
                class="bg-white dark:bg-neutral-900 mt-1 px-3 py-2 border border-neutral-200 focus:border-neutral-400 dark:border-neutral-800 rounded-lg focus:outline-none w-full text-neutral-900 dark:text-white text-sm" />
            @error('email') <div class="mt-1 text-red-500 text-xs">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-neutral-700 dark:text-neutral-300 text-sm">{{ __('Mot de passe') }}</label>
            <input name="password" type="password" required
                class="bg-white dark:bg-neutral-900 mt-1 px-3 py-2 border border-neutral-200 focus:border-neutral-400 dark:border-neutral-800 rounded-lg focus:outline-none w-full text-neutral-900 dark:text-white text-sm" />
            @error('password') <div class="mt-1 text-red-500 text-xs">{{ $message }}</div> @enderror
        </div>

        <div class="flex justify-between items-center text-sm">
            <label class="inline-flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                <input type="checkbox" name="remember" class="border-neutral-300 dark:border-neutral-700 rounded" />
                {{ __('Se souvenir de moi') }}
            </label>
        </div>

        <button type="submit"
            class="bg-neutral-900 hover:bg-neutral-800 dark:bg-white px-4 py-2 rounded-lg w-full font-medium text-white dark:text-neutral-900 text-sm">
            {{ __('Connexion') }}
        </button>
    </form>
</x-layouts::auth.simple>
