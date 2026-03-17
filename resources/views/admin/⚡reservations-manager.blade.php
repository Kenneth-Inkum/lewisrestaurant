<?php

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Reservations')] class extends Component
{
    public string $search = '';
    public string $filterDate = '';
    public string $filterStatus = '';

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public int $partySize = 2;
    public string $reservationDate = '';
    public string $reservationTime = '';
    public string $status = '';
    public string $notes = '';

    public function mount(): void
    {
        $this->filterDate = today()->format('Y-m-d');
        $this->status = ReservationStatus::Pending->value;
    }

    public function reservations(): mixed
    {
        return Reservation::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->filterDate, fn ($q) => $q->whereDate('reservation_date', $this->filterDate))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetForm();

        if ($id) {
            $r = Reservation::findOrFail($id);
            $this->editingId = $id;
            $this->name = $r->name;
            $this->email = $r->email;
            $this->phone = $r->phone;
            $this->partySize = $r->party_size;
            $this->reservationDate = $r->reservation_date->format('Y-m-d');
            $this->reservationTime = $r->reservation_time;
            $this->status = $r->status->value;
            $this->notes = $r->notes ?? '';
        } else {
            $this->reservationDate = today()->format('Y-m-d');
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'partySize' => ['required', 'integer', 'min:1'],
            'reservationDate' => ['required', 'date'],
            'reservationTime' => ['required'],
            'status' => ['required'],
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'party_size' => $this->partySize,
            'reservation_date' => $this->reservationDate,
            'reservation_time' => $this->reservationTime,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Reservation::findOrFail($this->editingId)->update($data);
        } else {
            Reservation::create($data);
        }

        $this->showModal = false;
    }

    public function updateStatus(int $id, string $status): void
    {
        Reservation::findOrFail($id)->update(['status' => $status]);
    }

    public function delete(int $id): void
    {
        Reservation::findOrFail($id)->delete();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = $this->email = $this->phone = '';
        $this->reservationDate = $this->reservationTime = $this->notes = '';
        $this->partySize = 2;
        $this->status = ReservationStatus::Pending->value;
    }

    public function statuses(): array
    {
        return ReservationStatus::cases();
    }

    public function availableTimes(): array
    {
        return [
            '17:00' => '5:00 PM', '17:30' => '5:30 PM', '18:00' => '6:00 PM',
            '18:30' => '6:30 PM', '19:00' => '7:00 PM', '19:30' => '7:30 PM',
            '20:00' => '8:00 PM', '20:30' => '8:30 PM', '21:00' => '9:00 PM',
            '21:30' => '9:30 PM',
        ];
    }
};
?>

<div>
    <flux:main class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Reservations</flux:heading>
                <flux:text class="mt-1">Manage guest bookings</flux:text>
            </div>
            <flux:button wire:click="openModal" variant="primary" icon="plus">
                New Reservation
            </flux:button>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col gap-3 sm:flex-row">
            <flux:input wire:model.live.debounce="search" placeholder="Search by name or email..." icon="magnifying-glass" class="sm:max-w-xs" />
            <flux:input wire:model.live="filterDate" type="date" class="sm:max-w-[180px]" />
            <flux:select wire:model.live="filterStatus" class="sm:max-w-[180px]">
                <option value="">All Statuses</option>
                @foreach($this->statuses() as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </flux:select>
            @if($filterDate || $filterStatus)
            <flux:button wire:click="$set('filterDate', ''); $set('filterStatus', '')" variant="ghost" size="sm">
                Clear filters
            </flux:button>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                <flux:heading size="lg">{{ $this->reservations()->count() }} Reservations</flux:heading>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Guest</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Date & Time</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Party</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($this->reservations() as $reservation)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50" wire:key="res-{{ $reservation->id }}">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $reservation->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $reservation->email }}</p>
                                <p class="text-xs text-zinc-500">{{ $reservation->phone }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $reservation->reservation_date->format('M j, Y') }}
                                </p>
                                <p class="text-xs text-zinc-500">
                                    {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A') }}
                                </p>
                            </td>
                            <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">
                                {{ $reservation->party_size }} guests
                            </td>
                            <td class="px-5 py-3.5">
                                <flux:select wire:change="updateStatus({{ $reservation->id }}, $event.target.value)"
                                             class="text-xs !py-1 !px-2 w-auto">
                                    @foreach($this->statuses() as $s)
                                    <option value="{{ $s->value }}" @selected($reservation->status === $s)>
                                        {{ $s->label() }}
                                    </option>
                                    @endforeach
                                </flux:select>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-end gap-1">
                                    <flux:button wire:click="openModal({{ $reservation->id }})" variant="ghost" size="sm" icon="pencil" />
                                    <flux:button wire:click="delete({{ $reservation->id }})" wire:confirm="Delete this reservation?" variant="ghost" size="sm" icon="trash" class="text-red-500" />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-zinc-500">
                                No reservations found for the selected filters.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </flux:main>

    {{-- Reservation Modal --}}
    <flux:modal wire:model="showModal" class="md:max-w-lg">
        <div class="space-y-5">
            <flux:heading size="lg">{{ $editingId ? 'Edit Reservation' : 'New Reservation' }}</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field class="sm:col-span-2">
                    <flux:label>Guest Name</flux:label>
                    <flux:input wire:model="name" placeholder="Full name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="email@example.com" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Phone</flux:label>
                    <flux:input wire:model="phone" type="tel" placeholder="+1 (202) 555-0100" />
                    <flux:error name="phone" />
                </flux:field>

                <flux:field>
                    <flux:label>Date</flux:label>
                    <flux:input wire:model="reservationDate" type="date" />
                    <flux:error name="reservationDate" />
                </flux:field>

                <flux:field>
                    <flux:label>Time</flux:label>
                    <flux:select wire:model="reservationTime">
                        <option value="">Select time...</option>
                        @foreach($this->availableTimes() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="reservationTime" />
                </flux:field>

                <flux:field>
                    <flux:label>Party Size</flux:label>
                    <flux:input wire:model="partySize" type="number" min="1" max="20" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        @foreach($this->statuses() as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Notes <span class="text-zinc-400">(optional)</span></flux:label>
                    <flux:textarea wire:model="notes" rows="2" placeholder="Special requests..." />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="save" variant="primary">
                    {{ $editingId ? 'Update' : 'Create' }} Reservation
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
