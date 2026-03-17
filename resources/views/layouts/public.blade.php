<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-zinc-950 text-zinc-100 antialiased">

        {{-- Demo banner --}}
        <div class="pointer-events-none fixed top-0 right-0 z-[200] size-28 overflow-hidden">
            <div class="absolute -right-7 top-7 w-36 rotate-45 bg-gold-400 py-1.5 text-center text-xs font-bold tracking-[0.2em] text-zinc-950 shadow-lg">
                DEMO
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="fixed top-0 z-50 w-full border-b border-zinc-800/50 bg-zinc-950/90 backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="flex h-18 items-center justify-between py-4">
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3">
                        <div class="flex size-9 items-center justify-center rounded-full border border-gold-400/40 bg-gold-400/10">
                            <span class="font-serif text-sm font-semibold text-gold-400">L</span>
                        </div>
                        <span class="font-serif text-lg font-semibold tracking-wide text-zinc-100">Lewis</span>
                    </a>

                    <div class="hidden items-center gap-8 md:flex">
                        <a href="{{ route('menu') }}" wire:navigate
                           class="text-sm font-medium tracking-widest text-zinc-400 uppercase transition-colors hover:text-gold-400 {{ request()->routeIs('menu') ? 'text-gold-400' : '' }}">
                            Menu
                        </a>
                        <a href="{{ route('reservations') }}" wire:navigate
                           class="text-sm font-medium tracking-widest text-zinc-400 uppercase transition-colors hover:text-gold-400 {{ request()->routeIs('reservations') ? 'text-gold-400' : '' }}">
                            Reservations
                        </a>
                        <a href="{{ route('contact') }}" wire:navigate
                           class="text-sm font-medium tracking-widest text-zinc-400 uppercase transition-colors hover:text-gold-400 {{ request()->routeIs('contact') ? 'text-gold-400' : '' }}">
                            Contact
                        </a>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('reservations') }}" wire:navigate
                           class="hidden rounded-full border border-gold-400 px-5 py-2 text-xs font-semibold tracking-widest text-gold-400 uppercase transition-all hover:bg-gold-400 hover:text-zinc-950 md:block">
                            Book a Table
                        </a>

                        {{-- Mobile menu toggle --}}
                        <button
                            x-data="{ open: false }"
                            @click="open = !open"
                            class="rounded p-1 text-zinc-400 md:hidden"
                            x-ref="menuButton"
                        >
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-zinc-800 bg-zinc-950 py-16">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-12 md:grid-cols-3">
                    <div>
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex size-8 items-center justify-center rounded-full border border-gold-400/40 bg-gold-400/10">
                                <span class="font-serif text-xs font-semibold text-gold-400">L</span>
                            </div>
                            <span class="font-serif text-base font-semibold text-zinc-100">Lewis Restaurant</span>
                        </div>
                        <p class="text-sm leading-relaxed text-zinc-500">
                            A modern fine dining experience crafted for every occasion.
                        </p>
                    </div>

                    <div>
                        <h4 class="mb-4 text-xs font-semibold tracking-widest text-zinc-400 uppercase">Hours</h4>
                        <div class="space-y-2 text-sm text-zinc-500">
                            <div class="flex justify-between">
                                <span>Monday – Thursday</span>
                                <span>5:00 – 10:00 PM</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Friday – Saturday</span>
                                <span>5:00 – 11:00 PM</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sunday</span>
                                <span>4:00 – 9:00 PM</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-4 text-xs font-semibold tracking-widest text-zinc-400 uppercase">Visit Us</h4>
                        <address class="space-y-1 text-sm not-italic text-zinc-500">
                            <p>1234 Culinary Avenue</p>
                            <p>Washington, DC 20001</p>
                            <p class="mt-3">
                                <a href="tel:+12025550100" class="transition-colors hover:text-gold-400">+1 (202) 555-0100</a>
                            </p>
                            <p>
                                <a href="mailto:reservations@lewisrestaurant.com" class="transition-colors hover:text-gold-400">
                                    reservations@lewisrestaurant.com
                                </a>
                            </p>
                        </address>
                    </div>
                </div>

                <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-zinc-800 pt-8 sm:flex-row">
                    <p class="text-xs text-zinc-600">© {{ date('Y') }} Lewis Restaurant. All rights reserved. Built with ❤️ by <a href="mailto:xanthosoma1989@gmail.com" class="text-gold-400 hover:underline">Kenneth Ekow Inkum</a></p>
                    <div class="flex gap-6">
                        <a href="{{ route('menu') }}" wire:navigate class="text-xs text-zinc-600 transition-colors hover:text-zinc-400">Menu</a>
                        <a href="{{ route('reservations') }}" wire:navigate class="text-xs text-zinc-600 transition-colors hover:text-zinc-400">Reservations</a>
                        <a href="{{ route('contact') }}" wire:navigate class="text-xs text-zinc-600 transition-colors hover:text-zinc-400">Contact</a>
                        @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-xs text-zinc-600 transition-colors hover:text-zinc-400">Admin</a>
                        @endauth
                    </div>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
