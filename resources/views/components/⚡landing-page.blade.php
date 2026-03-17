<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::public')] class extends Component
{
    //
};
?>

<div>
    {{-- Hero Section --}}
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden">
        {{-- Background image --}}
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1920&q=80"
             alt="Lewis Restaurant interior"
             class="absolute inset-0 h-full w-full object-cover" />
        {{-- Dark overlay --}}
        <div class="absolute inset-0 bg-zinc-950/75"></div>
        <div class="absolute inset-0 bg-linear-to-b from-zinc-950/40 via-transparent to-zinc-950"></div>
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 size-[600px] rounded-full bg-gold-400/10 blur-[120px]"></div>

        <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
            <p class="mb-6 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Washington, DC</p>
            <h1 class="mb-6 font-serif text-6xl font-bold leading-tight tracking-tight text-zinc-50 md:text-7xl lg:text-8xl">
                Lewis<br>Restaurant
            </h1>
            <p class="mx-auto mb-10 max-w-xl text-lg leading-relaxed text-zinc-300">
                A modern fine dining experience where exceptional cuisine meets timeless hospitality. Every dish tells a story.
            </p>
            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('reservations') }}" wire:navigate
                   class="rounded-full bg-gold-400 px-8 py-3.5 text-sm font-semibold tracking-widest text-zinc-950 uppercase transition-all hover:bg-gold-300">
                    Reserve a Table
                </a>
                <a href="{{ route('menu') }}" wire:navigate
                   class="rounded-full border border-zinc-400/40 px-8 py-3.5 text-sm font-semibold tracking-widest text-zinc-200 uppercase backdrop-blur-sm transition-all hover:border-zinc-200 hover:text-white">
                    View Our Menu
                </a>
            </div>
        </div>

        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </section>

    {{-- Signature Dishes (image cards) --}}
    <section class="bg-zinc-900 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mb-16 text-center">
                <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Chef's Selection</p>
                <h2 class="font-serif text-4xl font-bold text-zinc-50 md:text-5xl">Signature Dishes</h2>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach(\App\Models\MenuItem::with('category')->featured()->available()->take(4)->get() as $item)
                <div class="group relative aspect-[3/4] overflow-hidden rounded-2xl">
                    <img src="{{ $item->image_url }}"
                         alt="{{ $item->name }}"
                         class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6">
                        <p class="mb-1 text-xs font-semibold tracking-widest text-gold-400 uppercase">{{ $item->category->name }}</p>
                        <h3 class="mb-1.5 font-serif text-lg font-semibold text-zinc-50">{{ $item->name }}</h3>
                        <p class="mb-3 line-clamp-2 text-sm leading-relaxed text-zinc-400">{{ $item->description }}</p>
                        <p class="font-serif text-xl font-semibold text-gold-400">${{ number_format($item->price, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('menu') }}" wire:navigate
                   class="inline-flex items-center gap-2 text-sm font-medium tracking-widest text-gold-400 uppercase transition-colors hover:text-gold-300">
                    Explore Full Menu
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Gallery Strip --}}
    <section class="bg-zinc-950">
        <div class="grid grid-cols-2 lg:grid-cols-4">
            <div class="aspect-square overflow-hidden">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80"
                     alt="Restaurant cuisine"
                     class="h-full w-full object-cover transition-transform duration-700 hover:scale-105" />
            </div>
            <div class="aspect-square overflow-hidden">
                <img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?auto=format&fit=crop&w=600&q=80"
                     alt="Fine dining plate"
                     class="h-full w-full object-cover transition-transform duration-700 hover:scale-105" />
            </div>
            <div class="aspect-square overflow-hidden">
                <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=600&q=80"
                     alt="Restaurant ambiance"
                     class="h-full w-full object-cover transition-transform duration-700 hover:scale-105" />
            </div>
            <div class="aspect-square overflow-hidden">
                <img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=600&q=80"
                     alt="Wine and dining"
                     class="h-full w-full object-cover transition-transform duration-700 hover:scale-105" />
            </div>
        </div>
    </section>

    {{-- About / Story --}}
    <section class="bg-zinc-950 py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid items-center gap-16 lg:grid-cols-2">
                <div>
                    <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Our Story</p>
                    <h2 class="mb-6 font-serif text-4xl font-bold text-zinc-50 md:text-5xl">Crafted with<br>Passion & Purpose</h2>
                    <p class="mb-6 text-base leading-relaxed text-zinc-400">
                        Lewis Restaurant was founded on the belief that exceptional dining goes beyond the plate. It's about the warmth of welcome, the care in every detail, and the memory that lingers long after the last bite.
                    </p>
                    <p class="mb-8 text-base leading-relaxed text-zinc-400">
                        Our chef sources only the finest seasonal ingredients — from local farms and sustainable fisheries — to craft a menu that evolves with the harvest and honors each ingredient's full potential.
                    </p>

                    <div class="mb-8 grid grid-cols-3 gap-6 border-t border-zinc-800 pt-8">
                        <div>
                            <p class="font-serif text-3xl font-bold text-gold-400">15+</p>
                            <p class="mt-1 text-xs text-zinc-500">Years of Excellence</p>
                        </div>
                        <div>
                            <p class="font-serif text-3xl font-bold text-gold-400">{{ \App\Models\MenuItem::available()->count() }}</p>
                            <p class="mt-1 text-xs text-zinc-500">Menu Items</p>
                        </div>
                        <div>
                            <p class="font-serif text-3xl font-bold text-gold-400">4.9</p>
                            <p class="mt-1 text-xs text-zinc-500">Average Rating</p>
                        </div>
                    </div>

                    <a href="{{ route('contact') }}" wire:navigate
                       class="inline-flex items-center gap-2 text-sm font-medium tracking-widest text-gold-400 uppercase transition-colors hover:text-gold-300">
                        Learn More
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                {{-- Staggered image pair --}}
                <div class="relative h-[560px]">
                    <div class="absolute left-0 top-0 h-80 w-3/4 overflow-hidden rounded-2xl">
                        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80"
                             alt="Restaurant interior"
                             class="h-full w-full object-cover" />
                    </div>
                    <div class="absolute bottom-0 right-0 h-72 w-3/4 overflow-hidden rounded-2xl border-4 border-zinc-950">
                        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80"
                             alt="Fine dining atmosphere"
                             class="h-full w-full object-cover" />
                    </div>
                    {{-- Gold accent dot --}}
                    <div class="absolute left-[72%] top-[52%] size-6 rounded-full bg-gold-400"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Reservation CTA with background image --}}
    <section class="relative overflow-hidden py-32">
        <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1920&q=80"
             alt="Restaurant atmosphere"
             class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-zinc-950/80"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/60 via-transparent to-zinc-950/60"></div>
        <div class="relative mx-auto max-w-3xl px-6 text-center">
            <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Join Us This Evening</p>
            <h2 class="mb-6 font-serif text-4xl font-bold text-zinc-50 md:text-5xl">Reserve Your Table</h2>
            <p class="mb-10 text-lg text-zinc-300">
                Whether it's an intimate dinner for two or a celebration with loved ones, we're ready to make your evening unforgettable.
            </p>
            <a href="{{ route('reservations') }}" wire:navigate
               class="inline-block rounded-full bg-gold-400 px-10 py-4 text-sm font-semibold tracking-widest text-zinc-950 uppercase transition-all hover:bg-gold-300">
                Book Now
            </a>
        </div>
    </section>

    {{-- Hours & Location --}}
    <section class="border-t border-zinc-800 bg-zinc-950 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-8 sm:grid-cols-3">
                <div class="text-center">
                    <div class="mb-3 flex justify-center">
                        <svg class="size-6 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h4 class="mb-2 text-xs font-semibold tracking-widest text-zinc-400 uppercase">Hours</h4>
                    <p class="text-sm text-zinc-500">Mon–Thu: 5–10 PM</p>
                    <p class="text-sm text-zinc-500">Fri–Sat: 5–11 PM</p>
                    <p class="text-sm text-zinc-500">Sun: 4–9 PM</p>
                </div>
                <div class="text-center">
                    <div class="mb-3 flex justify-center">
                        <svg class="size-6 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </div>
                    <h4 class="mb-2 text-xs font-semibold tracking-widest text-zinc-400 uppercase">Location</h4>
                    <p class="text-sm text-zinc-500">1234 Culinary Avenue</p>
                    <p class="text-sm text-zinc-500">Washington, DC 20001</p>
                </div>
                <div class="text-center">
                    <div class="mb-3 flex justify-center">
                        <svg class="size-6 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                    </div>
                    <h4 class="mb-2 text-xs font-semibold tracking-widest text-zinc-400 uppercase">Reservations</h4>
                    <p class="text-sm text-zinc-500">+1 (202) 555-0100</p>
                    <p class="text-sm text-zinc-500">reservations@lewisrestaurant.com</p>
                </div>
            </div>
        </div>
    </section>
</div>
