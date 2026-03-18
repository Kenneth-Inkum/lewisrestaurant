<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public')] #[Title('Contact')] class extends Component
{
    //
};
?>

<div>
    {{-- Header --}}
    <div class="relative overflow-hidden pb-16 pt-32">
        <img src="https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?auto=format&fit=crop&w=1920&q=80"
             alt="Washington DC"
             class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-zinc-950/85"></div>
        <div class="absolute inset-0 bg-linear-to-b from-zinc-950/60 to-zinc-950"></div>
        <div class="relative mx-auto max-w-7xl px-6 text-center lg:px-8">
            <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Get In Touch</p>
            <h1 class="font-serif text-5xl font-bold text-zinc-50 md:text-6xl">Contact Us</h1>
            <p class="mx-auto mt-4 max-w-lg text-base text-zinc-300">
                We'd love to hear from you. Reach out for reservations, private events, or any questions.
            </p>
        </div>
    </div>

    <div class="bg-zinc-950 pb-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2">

                {{-- Contact Info --}}
                <div class="space-y-8">
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-8">
                        <div class="mb-4 flex size-11 items-center justify-center rounded-full bg-gold-400/10">
                            <svg class="size-5 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-serif text-lg font-semibold text-zinc-100">Location</h3>
                        <p class="text-zinc-400">1234 Culinary Avenue</p>
                        <p class="text-zinc-400">Washington, DC 20001</p>
                        <p class="mt-3 text-sm text-zinc-600">Valet parking available nightly</p>
                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-8">
                        <div class="mb-4 flex size-11 items-center justify-center rounded-full bg-gold-400/10">
                            <svg class="size-5 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="mb-3 font-serif text-lg font-semibold text-zinc-100">Hours of Operation</h3>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between border-b border-zinc-800 pb-2.5 text-zinc-400">
                                <span>Monday – Thursday</span>
                                <span>5:00 – 10:00 PM</span>
                            </div>
                            <div class="flex justify-between border-b border-zinc-800 pb-2.5 text-zinc-400">
                                <span>Friday – Saturday</span>
                                <span>5:00 – 11:00 PM</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>Sunday</span>
                                <span>4:00 – 9:00 PM</span>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-zinc-600">Kitchen closes 45 minutes before closing time</p>
                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-8">
                        <div class="mb-4 flex size-11 items-center justify-center rounded-full bg-gold-400/10">
                            <svg class="size-5 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <h3 class="mb-3 font-serif text-lg font-semibold text-zinc-100">Contact</h3>
                        <div class="space-y-2 text-sm">
                            <p>
                                <span class="text-zinc-500">Phone: </span>
                                <a href="tel:+12025550100" class="text-zinc-300 transition-colors hover:text-gold-400">+1 (202) 555-0100</a>
                            </p>
                            <p>
                                <span class="text-zinc-500">Email: </span>
                                <a href="mailto:reservations@lewisrestaurant.com" class="text-zinc-300 transition-colors hover:text-gold-400">
                                    reservations@lewisrestaurant.com
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Private Events / CTA --}}
                <div class="space-y-8">
                    <div class="rounded-2xl border border-gold-400/20 bg-gradient-to-br from-gold-400/5 to-transparent p-8">
                        <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Exclusive Experiences</p>
                        <h2 class="mb-4 font-serif text-3xl font-bold text-zinc-50">Private Dining</h2>
                        <p class="mb-6 leading-relaxed text-zinc-400">
                            Host your next celebration in our private dining room. Accommodating up to 12 guests, our dedicated team will craft a bespoke menu and dining experience tailored to your event.
                        </p>
                        <ul class="mb-8 space-y-3 text-sm text-zinc-500">
                            <li class="flex items-center gap-2">
                                <span class="inline-block size-1.5 shrink-0 rounded-full bg-gold-400"></span>
                                Custom menu curation with our executive chef
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-block size-1.5 shrink-0 rounded-full bg-gold-400"></span>
                                Dedicated sommelier for wine pairings
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-block size-1.5 shrink-0 rounded-full bg-gold-400"></span>
                                Personalized décor and floral arrangements
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-block size-1.5 shrink-0 rounded-full bg-gold-400"></span>
                                AV equipment available upon request
                            </li>
                        </ul>
                        <a href="mailto:reservations@lewisrestaurant.com?subject=Private%20Dining%20Inquiry"
                           class="inline-block rounded-full bg-gold-400 px-7 py-3 text-sm font-semibold tracking-widest text-zinc-950 uppercase transition-all hover:bg-gold-300">
                            Inquire Now
                        </a>
                    </div>

                    {{-- Map --}}
                    <div class="overflow-hidden rounded-2xl border border-zinc-800">
                        <div class="relative h-64">
                            <img src="https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?auto=format&fit=crop&w=800&q=80"
                                 alt="Washington DC aerial view"
                                 class="h-full w-full object-cover" />
                            <div class="absolute inset-0 bg-zinc-950/50"></div>
                            {{-- Pin --}}
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="mx-auto mb-2 flex size-10 items-center justify-center rounded-full border-2 border-gold-400 bg-zinc-950/80">
                                        <svg class="size-5 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-zinc-100">1234 Culinary Avenue</p>
                                    <p class="text-xs text-zinc-400">Washington, DC 20001</p>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-zinc-800 bg-zinc-900 p-4 text-center">
                            <p class="text-xs text-zinc-500">Valet parking available · Metro: Gallery Place-Chinatown</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
