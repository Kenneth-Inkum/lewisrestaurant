<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">

        {{-- Demo banner --}}
        <div class="pointer-events-none fixed top-0 right-0 z-[200] size-28 overflow-hidden">
            <div class="absolute -right-7 top-7 w-36 rotate-45 bg-gold-400 py-1.5 text-center text-xs font-bold tracking-[0.2em] text-zinc-950 shadow-lg">
                DEMO
            </div>
        </div>
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5">
                    <div class="flex size-7 items-center justify-center rounded-full border border-amber-400/40 bg-amber-400/10">
                        <span class="font-serif text-xs font-semibold text-amber-400">L</span>
                    </div>
                    <span class="font-serif text-sm font-semibold text-zinc-800 dark:text-zinc-100">Lewis Restaurant</span>
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Overview')" class="grid">
                    <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Management')" class="grid">
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.reservations')" :current="request()->routeIs('admin.reservations')" wire:navigate>
                        {{ __('Reservations') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="table-cells" :href="route('admin.tables')" :current="request()->routeIs('admin.tables')" wire:navigate>
                        {{ __('Tables') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="shopping-bag" :href="route('admin.orders')" :current="request()->routeIs('admin.orders')" wire:navigate>
                        {{ __('Orders') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Content')" class="grid">
                    <flux:sidebar.item icon="bookmark-square" :href="route('admin.menu')" :current="request()->routeIs('admin.menu')" wire:navigate>
                        {{ __('Menu') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="arrow-top-right-on-square" :href="route('home')" wire:navigate>
                    {{ __('View Website') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
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
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
