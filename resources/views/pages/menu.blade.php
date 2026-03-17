<?php

use App\Models\MenuCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public')] #[Title('Menu')] class extends Component
{
    #[Computed]
    public function categories(): \Illuminate\Database\Eloquent\Collection
    {
        return MenuCategory::active()->with('availableItems')->get();
    }
};
?>

<div>
    {{-- Page Header --}}
    <div class="relative overflow-hidden pb-16 pt-32">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1920&q=80"
             alt="Our cuisine"
             class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-zinc-950/85"></div>
        <div class="absolute inset-0 bg-linear-to-b from-zinc-950/60 to-zinc-950"></div>
        <div class="relative mx-auto max-w-7xl px-6 text-center lg:px-8">
            <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Explore</p>
            <h1 class="font-serif text-5xl font-bold text-zinc-50 md:text-6xl">Our Menu</h1>
            <p class="mx-auto mt-4 max-w-lg text-base text-zinc-300">
                Seasonal ingredients. Timeless technique. Every dish crafted with intention.
            </p>
        </div>
    </div>

    {{-- Category Navigation --}}
    <div class="sticky top-[72px] z-40 border-b border-zinc-800 bg-zinc-950/95 backdrop-blur-md">
        <div class="mx-auto max-w-7xl overflow-x-auto px-6 lg:px-8">
            <div class="flex gap-1 py-3">
                @foreach($this->categories as $category)
                <a href="#category-{{ $category->id }}"
                   class="whitespace-nowrap rounded-full px-4 py-2 text-xs font-medium tracking-widest text-zinc-400 uppercase transition-all hover:bg-zinc-800 hover:text-zinc-100">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Menu Categories --}}
    <div class="bg-zinc-950 pb-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @foreach($this->categories as $category)
            <div id="category-{{ $category->id }}" class="py-16">
                {{-- Category Heading --}}
                <div class="mb-10 flex items-end justify-between border-b border-zinc-800 pb-6">
                    <div>
                        <h2 class="font-serif text-3xl font-bold text-zinc-100">{{ $category->name }}</h2>
                        @if($category->description)
                        <p class="mt-1 text-sm text-zinc-500">{{ $category->description }}</p>
                        @endif
                    </div>
                    <span class="text-xs text-zinc-600">{{ $category->availableItems->count() }} items</span>
                </div>

                {{-- Items Grid --}}
                <div class="grid gap-px overflow-hidden rounded-2xl bg-zinc-800 sm:grid-cols-2">
                    @foreach($category->availableItems as $item)
                    <div class="group relative bg-zinc-950 transition-colors hover:bg-zinc-900">
                        @if($item->image_url)
                        <div class="relative overflow-hidden">
                            <img src="{{ $item->image_url }}"
                                 alt="{{ $item->name }}"
                                 class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 to-transparent"></div>
                            @if($item->is_featured)
                            <span class="absolute right-3 top-3 rounded-full bg-gold-400/90 px-2.5 py-0.5 text-xs font-semibold text-zinc-950">
                                Featured
                            </span>
                            @endif
                        </div>
                        @endif

                        <div class="p-6">
                            @if(!$item->image_url && $item->is_featured)
                            <span class="mb-2 inline-block rounded-full bg-gold-400/10 px-2 py-0.5 text-xs font-medium text-gold-400">
                                Featured
                            </span>
                            @endif

                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="font-serif text-lg font-semibold text-zinc-100 group-hover:text-white">
                                        {{ $item->name }}
                                    </h3>
                                    <p class="mt-1.5 text-sm leading-relaxed text-zinc-500">{{ $item->description }}</p>

                                    @if($item->dietary_tags)
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach($item->dietary_tags as $tag)
                                        <span class="rounded-full border border-zinc-800 bg-zinc-900 px-2.5 py-0.5 text-xs text-zinc-500">
                                            {{ $tag }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <p class="shrink-0 font-serif text-lg font-semibold text-gold-400">
                                    ${{ number_format($item->price, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if(!$loop->last)
            <div class="border-t border-zinc-900"></div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Bottom CTA --}}
    <div class="border-t border-zinc-800 bg-zinc-900 py-16">
        <div class="mx-auto max-w-2xl px-6 text-center">
            <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Ready to Dine?</p>
            <h3 class="mb-6 font-serif text-3xl font-bold text-zinc-50">Make Your Reservation</h3>
            <a href="{{ route('reservations') }}" wire:navigate
               class="inline-block rounded-full bg-gold-400 px-8 py-3.5 text-sm font-semibold tracking-widest text-zinc-950 uppercase transition-all hover:bg-gold-300">
                Book a Table
            </a>
        </div>
    </div>
</div>
