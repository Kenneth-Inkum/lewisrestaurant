<?php

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public')] #[Title('Reservations')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public int $partySize = 2;
    public string $reservationDate = '';
    public string $reservationTime = '';
    public string $notes = '';
    public bool $submitted = false;

    public function mount(): void
    {
        $this->reservationDate = now()->addDay()->format('Y-m-d');
    }

    public function availableTimes(): array
    {
        return [
            '17:00' => '5:00 PM',
            '17:30' => '5:30 PM',
            '18:00' => '6:00 PM',
            '18:30' => '6:30 PM',
            '19:00' => '7:00 PM',
            '19:30' => '7:30 PM',
            '20:00' => '8:00 PM',
            '20:30' => '8:30 PM',
            '21:00' => '9:00 PM',
            '21:30' => '9:30 PM',
        ];
    }

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'partySize' => ['required', 'integer', 'min:1', 'max:20'],
            'reservationDate' => ['required', 'date', 'after:today'],
            'reservationTime' => ['required', 'string'],
        ]);

        Reservation::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'party_size' => $this->partySize,
            'reservation_date' => $this->reservationDate,
            'reservation_time' => $this->reservationTime,
            'notes' => $this->notes,
            'status' => ReservationStatus::Pending,
        ]);

        $this->submitted = true;
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->reservationDate = now()->addDay()->format('Y-m-d');
        $this->submitted = false;
    }
};
?>

<div>

    {{-- Header --}}
    <div class="bg-zinc-950 pb-16 pt-32">
        <div class="mx-auto max-w-7xl px-6 text-center lg:px-8">
            <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-gold-400 uppercase">Dine With Us</p>
            <h1 class="font-serif text-5xl font-bold text-zinc-50 md:text-6xl">Reservations</h1>
            <p class="mx-auto mt-4 max-w-lg text-base text-zinc-400">
                Reserve your table and let us take care of the rest. We look forward to hosting you.
            </p>
        </div>
    </div>

    <div class="bg-zinc-950 pb-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-16 lg:grid-cols-5">

                {{-- Booking Form --}}
                <div class="lg:col-span-3">
                    @if($submitted)
                    {{-- Success State --}}
                    <div class="rounded-2xl border border-gold-400/20 bg-gold-400/5 p-10 text-center">
                        <div class="mx-auto mb-6 flex size-16 items-center justify-center rounded-full border border-gold-400/30 bg-gold-400/10">
                            <svg class="size-7 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-serif text-2xl font-bold text-zinc-50">Reservation Received</h3>
                        <p class="mb-2 text-zinc-400">
                            Thank you, <span class="text-zinc-200">{{ $name }}</span>. Your reservation request has been submitted.
                        </p>
                        <p class="mb-8 text-sm text-zinc-500">
                            We'll confirm your booking at <span class="text-zinc-300">{{ $email }}</span> within 2 hours.
                        </p>
                        <button wire:click="resetForm"
                                class="rounded-full border border-zinc-700 px-6 py-2.5 text-sm font-medium text-zinc-400 transition-colors hover:border-zinc-500 hover:text-zinc-200">
                            Make Another Reservation
                        </button>
                    </div>
                    @else
                    {{-- Form --}}
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-8">
                        <h2 class="mb-8 font-serif text-2xl font-semibold text-zinc-100">Your Details</h2>

                        <form wire:submit="submit" class="space-y-6">
                            <div class="grid gap-6 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">Full Name</flux:label>
                                    <flux:input wire:model="name" type="text" placeholder="Jane Smith" />
                                    <flux:error name="name" />
                                </flux:field>

                                <flux:field>
                                    <flux:label class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">Email</flux:label>
                                    <flux:input wire:model="email" type="email" placeholder="jane@example.com" />
                                    <flux:error name="email" />
                                </flux:field>
                            </div>

                            <div class="grid gap-6 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">Phone</flux:label>
                                    <flux:input wire:model="phone" type="tel" placeholder="+1 (202) 555-0100" />
                                    <flux:error name="phone" />
                                </flux:field>

                                <flux:field>
                                    <flux:label class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">Party Size</flux:label>
                                    <flux:select wire:model="partySize" variant="listbox">
                                        @for($i = 1; $i <= 12; $i++)
                                        <flux:select.option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Guest' : 'Guests' }}</flux:select.option>
                                        @endfor
                                        <flux:select.option value="13">13+ Guests (call us)</flux:select.option>
                                    </flux:select>
                                    <flux:error name="partySize" />
                                </flux:field>
                            </div>

                            <div class="grid gap-6 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">Date</flux:label>
                                    <flux:input wire:model="reservationDate" type="date" min="{{ now()->addDay()->format('Y-m-d') }}" />
                                    <flux:error name="reservationDate" />
                                </flux:field>

                                <flux:field>
                                    <flux:label class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">Time</flux:label>
                                    <flux:select wire:model="reservationTime" variant="listbox" placeholder="Select a time...">
                                        @foreach($this->availableTimes() as $value => $label)
                                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="reservationTime" />
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label class="text-xs font-semibold tracking-widest text-zinc-400 uppercase">
                                    Special Requests <span class="normal-case text-zinc-600">(optional)</span>
                                </flux:label>
                                <flux:textarea wire:model="notes" rows="3" placeholder="Allergies, anniversaries, dietary preferences..." />
                            </flux:field>

                            <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                                <span wire:loading.remove>Request Reservation</span>
                                <span wire:loading>Submitting...</span>
                            </flux:button>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Info Panel --}}
                <div class="space-y-6 lg:col-span-2">
                    {{-- Atmospheric image --}}
                    <div class="overflow-hidden rounded-2xl">
                        <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=80"
                             alt="Restaurant atmosphere"
                             class="h-52 w-full object-cover" />
                    </div>
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6">
                        <h3 class="mb-4 font-serif text-lg font-semibold text-zinc-100">Hours</h3>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between text-zinc-400">
                                <span>Monday – Thursday</span>
                                <span>5:00 – 10:00 PM</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>Friday – Saturday</span>
                                <span>5:00 – 11:00 PM</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>Sunday</span>
                                <span>4:00 – 9:00 PM</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6">
                        <h3 class="mb-4 font-serif text-lg font-semibold text-zinc-100">Policies</h3>
                        <ul class="space-y-3 text-sm text-zinc-500">
                            <li class="flex gap-2.5">
                                <svg class="mt-0.5 size-4 shrink-0 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Reservations held for 15 minutes past booking time
                            </li>
                            <li class="flex gap-2.5">
                                <svg class="mt-0.5 size-4 shrink-0 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Please notify us of any dietary restrictions
                            </li>
                            <li class="flex gap-2.5">
                                <svg class="mt-0.5 size-4 shrink-0 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                For parties of 8 or more, please call directly
                            </li>
                            <li class="flex gap-2.5">
                                <svg class="mt-0.5 size-4 shrink-0 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Cancellations accepted up to 24 hours in advance
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6">
                        <h3 class="mb-4 font-serif text-lg font-semibold text-zinc-100">Private Events</h3>
                        <p class="mb-4 text-sm text-zinc-500">
                            Planning a special occasion? Our private dining room accommodates up to 12 guests for an intimate, exclusive experience.
                        </p>
                        <a href="{{ route('contact') }}" wire:navigate
                           class="text-sm font-medium text-gold-400 transition-colors hover:text-gold-300">
                            Inquire about private events →
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
