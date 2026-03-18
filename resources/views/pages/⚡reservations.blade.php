<?php

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Services\ReservationService;
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
        return ReservationService::getAvailableTimes();
    }

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ReservationService::getPhoneValidationRule(),
            'partySize' => ReservationService::getPartySizeValidationRule(),
            'reservationDate' => ReservationService::getPublicDateValidationRule(),
            'reservationTime' => ['required', 'string'],
        ]);

        try {
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
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit reservation. Please try again.');
            \Log::error('Reservation submission failed: ' . $e->getMessage());
        }
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
    {{-- Hero Section --}}
    <div class="bg-linear-to-br from-zinc-900 via-zinc-800 to-zinc-900 pb-16 pt-32">
        <div class="mx-auto max-w-7xl px-6 text-center lg:px-8">
            <flux:badge variant="outline" class="mb-4 text-gold-400 border-gold-400/30">
                Dine With Us
            </flux:badge>
            <flux:heading size="4xl" class="text-zinc-50 font-serif mb-6">
                Make a Reservation
            </flux:heading>
            <flux:text class="text-zinc-400 text-lg max-w-2xl mx-auto">
                Reserve your table and let us take care of the rest. We look forward to hosting you for an unforgettable dining experience.
            </flux:text>
        </div>
    </div>

    <div class="bg-zinc-950 pb-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-3">

                {{-- Booking Form --}}
                <div class="lg:col-span-2">
                    @if($submitted)
                    {{-- Success State --}}
                    <flux:container class="bg-linear-to-br from-green-600/10 to-emerald-600/10 border-green-600/20">
                        <flux:container.inner class="p-12 text-center">
                            <div class="mx-auto mb-8 flex size-20 items-center justify-center rounded-full bg-green-600/20 border border-green-600/30">
                                <flux:icon name="check-circle" class="size-10 text-green-400" />
                            </div>
                            <flux:heading size="2xl" class="text-zinc-50 mb-4">Reservation Received!</flux:heading>
                            <flux:text class="text-zinc-300 mb-6 text-lg">
                                Thank you, <span class="text-zinc-100 font-semibold">{{ $name }}</span>. Your reservation request has been submitted successfully.
                            </flux:text>
                            <flux:text class="text-zinc-400 mb-8">
                                We'll confirm your booking at <span class="text-zinc-200">{{ $email }}</span> within 2 hours.
                            </flux:text>
                            <flux:button 
                                wire:click="resetForm" 
                                variant="outline"
                                icon="plus"
                                class="border-zinc-600 text-zinc-300 hover:border-zinc-500 hover:text-zinc-200"
                            >
                                Make Another Reservation
                            </flux:button>
                        </flux:container.inner>
                    </flux:container>
                    @else
                    {{-- Form --}}
                    <flux:container class="border-zinc-800">
                        <flux:container.inner class="p-8">
                            <flux:heading size="xl" class="text-zinc-100 mb-8">Reserve Your Table</flux:heading>

                            <form wire:submit="submit" class="space-y-8">
                                {{-- Guest Information --}}
                                <flux:fieldset>
                                    <flux:fieldset.legend>Guest Information</flux:fieldset.legend>
                                    
                                    <div class="grid gap-6 sm:grid-cols-2">
                                        <flux:field>
                                            <flux:label>Full Name</flux:label>
                                            <flux:input 
                                                wire:model="name" 
                                                type="text" 
                                                placeholder="Jane Smith"
                                                icon="user"
                                            />
                                            <flux:error name="name" />
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>Email Address</flux:label>
                                            <flux:input 
                                                wire:model="email" 
                                                type="email" 
                                                placeholder="jane@example.com"
                                                icon="envelope"
                                            />
                                            <flux:error name="email" />
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>Phone Number</flux:label>
                                            <flux:input 
                                                wire:model="phone" 
                                                type="tel" 
                                                placeholder="+1 (555) 123-4567"
                                                icon="phone"
                                            />
                                            <flux:error name="phone" />
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>Party Size</flux:label>
                                            <flux:select 
                                                wire:model="partySize" 
                                                variant="listbox"
                                                icon="users"
                                            >
                                                @for($i = 1; $i <= App\Services\ReservationService::getMaxPartySize(); $i++)
                                                <flux:select.option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Guest' : 'Guests' }}</flux:select.option>
                                                @endfor
                                            </flux:select>
                                            <flux:error name="partySize" />
                                        </flux:field>
                                    </div>
                                </flux:fieldset>
                                
                                {{-- Reservation Details --}}
                                <flux:fieldset>
                                    <flux:fieldset.legend>Reservation Details</flux:fieldset.legend>
                                    
                                    <div class="grid gap-6 sm:grid-cols-2">
                                        <flux:field>
                                            <flux:label>Date</flux:label>
                                            <flux:input 
                                                wire:model="reservationDate" 
                                                type="date" 
                                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                                icon="calendar"
                                            />
                                            <flux:error name="reservationDate" />
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>Time</flux:label>
                                            <flux:select 
                                                wire:model="reservationTime" 
                                                variant="listbox"
                                                icon="clock"
                                            >
                                                @foreach($this->availableTimes() as $value => $label)
                                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                                @endforeach
                                            </flux:select>
                                            <flux:error name="reservationTime" />
                                        </flux:field>
                                    </div>

                                    <flux:field>
                                        <flux:label>Special Requests <span class="text-zinc-500">(optional)</span></flux:label>
                                        <flux:textarea 
                                            wire:model="notes" 
                                            rows="4" 
                                            placeholder="Dietary restrictions, special occasions, accessibility needs..."
                                        />
                                    </flux:field>
                                </flux:fieldset>

                                <flux:button 
                                    type="submit" 
                                    variant="primary" 
                                    class="w-full"
                                    wire:loading.attr="disabled"
                                    icon="calendar-plus"
                                >
                                    <span wire:loading.remove>
                                        Request Reservation
                                    </span>
                                    <span wire:loading>
                                        <flux:icon name="arrow-path" class="animate-spin mr-2" />
                                        Submitting...
                                    </span>
                                </flux:button>
                            </form>
                        </flux:container.inner>
                    </flux:container>
                    @endif
                </div>

                {{-- Info Panel --}}
                <div class="space-y-8">
                    {{-- Restaurant Image --}}
                    <flux:container class="overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=80"
                             alt="Restaurant atmosphere"
                             class="h-64 w-full object-cover" />
                    </flux:container>
                    
                    {{-- Hours --}}
                    <flux:container class="border-zinc-800">
                        <flux:container.inner class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <flux:icon name="clock" class="size-5 text-gold-400" />
                                <flux:heading size="lg" class="text-zinc-100">Hours</flux:heading>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <flux:text class="text-zinc-400">Monday – Thursday</flux:text>
                                    <flux:text class="text-zinc-300 font-medium">5:00 – 10:00 PM</flux:text>
                                </div>
                                <div class="flex justify-between items-center">
                                    <flux:text class="text-zinc-400">Friday – Saturday</flux:text>
                                    <flux:text class="text-zinc-300 font-medium">5:00 – 11:00 PM</flux:text>
                                </div>
                                <div class="flex justify-between items-center">
                                    <flux:text class="text-zinc-400">Sunday</flux:text>
                                    <flux:text class="text-zinc-300 font-medium">4:00 – 9:00 PM</flux:text>
                                </div>
                            </div>
                        </flux:container.inner>
                    </flux:container>

                    {{-- Policies --}}
                    <flux:container class="border-zinc-800">
                        <flux:container.inner class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <flux:icon name="clipboard-document-check" class="size-5 text-gold-400" />
                                <flux:heading size="lg" class="text-zinc-100">Policies</flux:heading>
                            </div>
                            <div class="space-y-4">
                                <div class="flex gap-3">
                                    <flux:icon name="check-circle" class="size-5 text-gold-400 mt-0.5 shrink-0" />
                                    <flux:text class="text-zinc-300 text-sm">Reservations held for 15 minutes past booking time</flux:text>
                                </div>
                                <div class="flex gap-3">
                                    <flux:icon name="check-circle" class="size-5 text-gold-400 mt-0.5 shrink-0" />
                                    <flux:text class="text-zinc-300 text-sm">Please notify us of any dietary restrictions</flux:text>
                                </div>
                                <div class="flex gap-3">
                                    <flux:icon name="check-circle" class="size-5 text-gold-400 mt-0.5 shrink-0" />
                                    <flux:text class="text-zinc-300 text-sm">For parties of 8 or more, please call directly</flux:text>
                                </div>
                                <div class="flex gap-3">
                                    <flux:icon name="check-circle" class="size-5 text-gold-400 mt-0.5 shrink-0" />
                                    <flux:text class="text-zinc-300 text-sm">Cancellations accepted up to 24 hours in advance</flux:text>
                                </div>
                            </div>
                        </flux:container.inner>
                    </flux:container>

                    {{-- Private Events --}}
                    <flux:container class="border-zinc-800">
                        <flux:container.inner class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <flux:icon name="calendar-days" class="size-5 text-gold-400" />
                                <flux:heading size="lg" class="text-zinc-100">Private Events</flux:heading>
                            </div>
                            <flux:text class="text-zinc-300 mb-4">
                                Planning a special occasion? Our private dining room accommodates up to 12 guests for an intimate, exclusive experience.
                            </flux:text>
                            <flux:button 
                                href="{{ route('contact') }}" 
                                wire:navigate
                                variant="outline"
                                icon="phone"
                                class="border-gold-400/30 text-gold-400 hover:border-gold-400 hover:text-gold-300"
                            >
                                Inquire about private events
                            </flux:button>
                        </flux:container.inner>
                    </flux:container>
                </div>

            </div>
        </div>
    </div>

</div>
