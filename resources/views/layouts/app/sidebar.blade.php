<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="bg-white dark:bg-zinc-800 min-h-screen">
    <flux:sidebar sticky collapsible="mobile"
        class="bg-zinc-50 dark:bg-zinc-900 border-e border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                <flux:sidebar.item wire:navigate>
                    Requests
                </flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('users.index')"
                    :current="request()->routeIs('users.index')" wire:navigate>
                    Users
                </flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('requests.index')"
                    :current="request()->routeIs('requests.index')" wire:navigate>
                    Requests
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 font-normal text-sm">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-sm text-start">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="flex-1 grid text-sm text-start leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/searchpanes/2.3.5/js/dataTables.searchPanes.js" defer></script>
    <script src="https://cdn.datatables.net/searchpanes/2.3.5/js/searchPanes.dataTables.js" defer></script>
    <script src="https://cdn.datatables.net/select/3.1.3/js/dataTables.select.js" defer></script>
    <script src="https://cdn.datatables.net/select/3.1.3/js/select.dataTables.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.dataTables.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.html5.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.print.min.js" defer></script>
    <script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>

    <script src="{{ asset('js/script-user-management.js') }}" defer></script>
    <script src="{{ asset('js/manage-user-details.js') }}" defer></script>
    <script src="{{ asset('js/script-analysis-request.js') }}" defer></script>
    <script>
        (function() {
            if (window.__analysisNotificationBooted) return;
            window.__analysisNotificationBooted = true;

            const userId = @json(auth()->id());
            if (!userId) return;

            function notifyAnalysisCompleted(event) {
                if (!event || !event.textAnalysisId) return;

                const status = String(event.status || '');
                const dedupeId = event.analysisRequestId ?? event.analysis_request_id ?? event.textAnalysisId;
                const detailUrl = event.analysisRequestId || event.analysis_request_id ?
                    `/analysis-requests/${event.analysisRequestId ?? event.analysis_request_id}` :
                    `/analyses/${event.textAnalysisId}`;
                const key = `analysis-notified-${dedupeId}-${status}`;
                if (localStorage.getItem(key) === '1') return;
                localStorage.setItem(key, '1');

                const title = status === 'completed' ? 'Analyse terminee' : 'Analyse echouee';
                const message = `L'analyse #${event.textAnalysisId} est ${status === 'completed' ? 'terminee' : 'en echec'}.`;

                if ('Notification' in window) {
                    if (Notification.permission === 'default') {
                        Notification.requestPermission();
                    }

                    if (Notification.permission === 'granted') {
                        const notification = new Notification(title, {
                            body: message,
                            icon: '/vinify.png'
                        });
                        notification.onclick = function(e) {
                            e.preventDefault();
                            window.open(detailUrl, '_blank');
                            notification.close();
                        };
                    }
                }

                if (window.Swal) {
                    Swal.fire({
                        title: title,
                        text: message,
                        icon: status === 'completed' ? 'success' : 'error',
                        confirmButtonText: 'Voir',
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            window.open(detailUrl, '_blank');
                        }
                    });
                }
            }

            function bindAnalysisChannelListener() {
                if (!window.Echo) {
                    setTimeout(bindAnalysisChannelListener, 1200);
                    return;
                }

                const channelName = `plagiarism-user.${userId}`;
                if (window.__analysisChannelName === channelName) return;

                window.__analysisChannelName = channelName;
                window.Echo.channel(channelName).listen('.analysis-completed', notifyAnalysisCompleted);
            }

            document.addEventListener('DOMContentLoaded', bindAnalysisChannelListener);
            document.addEventListener('livewire:navigated', bindAnalysisChannelListener);
            bindAnalysisChannelListener();
        })();
    </script>
    @fluxScripts
    @stack('scripts')
</body>

</html>
