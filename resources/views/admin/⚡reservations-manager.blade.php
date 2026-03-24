<?php

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Services\ReservationService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Reservations')] class extends Component {
    use WithPagination;
    public string $search = '';
    public string $filterDate = '';
    public string $filterStatus = '';
    public string $filterPartySize = '';

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
        // Set default filter to show today's reservations (all statuses)
        // This is more useful for restaurant managers to see today's bookings
        $this->filterDate = today()->format('Y-m-d');
        // Don't set status filter to show all statuses for today
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDate(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPartySize(): void
    {
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return !empty($this->search) || !empty($this->filterDate) || !empty($this->filterStatus) || !empty($this->filterPartySize);
    }

    public function getTodayCount(): int
    {
        return Reservation::whereDate('reservation_date', today())->count();
    }

    public function getPendingCount(): int
    {
        return Reservation::where('status', ReservationStatus::Pending->value)->count();
    }

    public function getConfirmedCount(): int
    {
        return Reservation::where('status', ReservationStatus::Confirmed->value)->count();
    }

    public function getTotalGuestsToday(): int
    {
        return Reservation::whereDate('reservation_date', today())->sum('party_size');
    }

    #[Computed]
    public function reservations(): mixed
    {
        $query = Reservation::query()
            ->when(
                $this->search,
                fn($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%"),
            )
            ->when($this->filterDate, fn($q) => $q->whereDate('reservation_date', $this->filterDate))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPartySize, function ($q) {
                match ($this->filterPartySize) {
                    '1-2' => $q->whereBetween('party_size', [1, 2]),
                    '3-4' => $q->whereBetween('party_size', [3, 4]),
                    '5-6' => $q->whereBetween('party_size', [5, 6]),
                    '7+' => $q->where('party_size', '>=', 7),
                    default => null,
                };
            });

        return $query->orderBy('reservation_date')->orderBy('reservation_time')->paginate(15);
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
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ReservationService::getPhoneValidationRule(),
            'partySize' => ReservationService::getPartySizeValidationRule(),
            'reservationDate' => ReservationService::getAdminDateValidationRule(),
            'reservationTime' => ['required', 'string'],
            'status' => ['required'],
        ]);

        try {
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
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save reservation. Please try again.');
            \Log::error('Reservation save failed: ' . $e->getMessage());
        }
    }

    public function updateStatus(int $id, string $status): void
    {
        try {
            Reservation::findOrFail($id)->update(['status' => $status]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update reservation status.');
            \Log::error('Reservation status update failed: ' . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            Reservation::findOrFail($id)->delete();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete reservation.');
            \Log::error('Reservation delete failed: ' . $e->getMessage());
        }
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
        return ReservationService::getAvailableTimes();
    }
};
?>

<div>
    <flux:main class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Reservations</flux:heading>
                <flux:text class="mt-1">Manage guest bookings and restaurant capacity</flux:text>
            </div>
            <flux:button wire:click="openModal" variant="primary" icon="plus">
                New Reservation
            </flux:button>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <flux:card>
                <div class="p-4 text-center">
                    <flux:heading size="lg">{{ $this->getTodayCount() }}</flux:heading>
                    <flux:text class="text-sm">Today</flux:text>
                </div>
            </flux:card>
            <flux:card>
                <div class="p-4 text-center">
                    <flux:heading size="lg">{{ $this->getPendingCount() }}</flux:heading>
                    <flux:text class="text-sm">Pending</flux:text>
                </div>
            </flux:card>
            <flux:card>
                <div class="p-4 text-center">
                    <flux:heading size="lg">{{ $this->getConfirmedCount() }}</flux:heading>
                    <flux:text class="text-sm">Confirmed</flux:text>
                </div>
            </flux:card>
            <flux:card>
                <div class="p-4 text-center">
                    <flux:heading size="lg">{{ $this->getTotalGuestsToday() }}</flux:heading>
                    <flux:text class="text-sm">Guests Today</flux:text>
                </div>
            </flux:card>
        </div>

        {{-- Advanced Filters --}}
        <flux:card class="p-6">
            <flux:heading size="lg" class="mb-4">Filter Reservations</flux:heading>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <flux:field>
                    <flux:label>Search</flux:label>
                    <flux:input wire:model.live.debounce="search" placeholder="Search guests..." icon="magnifying-glass"
                        clearable />
                </flux:field>

                <flux:field>
                    <flux:label>Date</flux:label>
                    <flux:input wire:model.live="filterDate" type="date" placeholder="All dates" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="filterStatus" variant="listbox" placeholder="All statuses">
                        @foreach ($this->statuses() as $s)
                            <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Party Size</flux:label>
                    <flux:select wire:model.live="filterPartySize" variant="listbox" placeholder="All sizes">
                        <flux:select.option value="1-2">1-2 guests</flux:select.option>
                        <flux:select.option value="3-4">3-4 guests</flux:select.option>
                        <flux:select.option value="5-6">5-6 guests</flux:select.option>
                        <flux:select.option value="7+">7+ guests</flux:select.option>
                    </flux:select>
                </flux:field>
            </div>

            @if ($this->hasActiveFilters())
                <div class="mt-4 flex items-center justify-between">
                    <flux:text class="text-sm text-zinc-500">
                        {{ $this->reservations->total() }} reservations found
                    </flux:text>
                    <flux:button
                        wire:click="$set('search', ''); $set('filterDate', ''); $set('filterStatus', ''); $set('filterPartySize', '')"
                        variant="ghost" size="sm" icon="x-mark">
                        Clear all filters
                    </flux:button>
                </div>
            @endif
        </flux:card>

        {{-- Reservations Table --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <flux:heading size="lg">Reservations</flux:heading>
                <flux:text class="text-sm text-zinc-500">
                    Showing {{ $this->reservations->count() }} of {{ $this->reservations->total() }}
                </flux:text>
            </div>

            <flux:table :paginate="$this->reservations">
                <flux:table.columns>
                    <flux:table.column>Guest Details</flux:table.column>
                    <flux:table.column>Reservation</flux:table.column>
                    <flux:table.column>Party</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->reservations as $reservation)
                        <flux:table.row wire:key="res-{{ $reservation->id }}">
                            <flux:table.cell>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <flux:avatar size="sm" :text="$reservation->name" />
                                        <div>
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $reservation->name }}</p>
                                            <p class="text-xs text-zinc-500">{{ $reservation->email }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-zinc-500">{{ $reservation->phone }}</p>
                                    @if ($reservation->notes)
                                        <p class="text-xs text-zinc-400 italic mt-1">
                                            {{ Str::limit($reservation->notes, 50) }}</p>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="calendar" class="size-4 text-zinc-400" />
                                        <span
                                            class="font-medium">{{ $reservation->reservation_date->format('M j, Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="clock" class="size-4 text-zinc-400" />
                                        <span
                                            class="text-sm">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A') }}</span>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge variant="outline">
                                    {{ $reservation->party_size }}
                                    {{ $reservation->party_size === 1 ? 'guest' : 'guests' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:select wire:change="updateStatus({{ $reservation->id }}, $event.target.value)"
                                    variant="listbox" class="text-xs">
                                    @foreach ($this->statuses() as $s)
                                        <flux:select.option value="{{ $s->value }}"
                                            :selected="$reservation->status === $s">
                                            {{ $s->label() }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

                                    <flux:menu align="end">
                                        <flux:menu.item wire:click="openModal({{ $reservation->id }})"
                                            icon="pencil">
                                            Edit Reservation
                                        </flux:menu.item>

                                        <flux:menu.item wire:click="delete({{ $reservation->id }})" icon="trash"
                                            variant="danger"
                                            wire:confirm="Are you sure you want to delete this reservation?">
                                            Delete Reservation
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="py-12">
                                <div class="text-center">
                                    <flux:icon name="calendar-x-mark" class="size-12 text-zinc-300 mx-auto mb-4" />
                                    <flux:heading size="md" class="text-zinc-600">No reservations found
                                    </flux:heading>
                                    <flux:text class="text-zinc-500 mt-2">
                                        Try adjusting your filters or create a new reservation.
                                    </flux:text>
                                    <flux:button wire:click="openModal" variant="outline" class="mt-4"
                                        icon="plus">
                                        Create Reservation
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            <div class="px-6 py-4 border-t">
                {{ $this->reservations->links() }}
            </div>
        </flux:card>

    {{-- Reservation Modal --}}
    <flux:modal wire:model="showModal" class="md:max-w-2xl">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? 'Edit Reservation' : 'New Reservation' }}</flux:heading>
            <flux:text>Create and manage guest reservations</flux:text>

            <div class="grid gap-6 sm:grid-cols-2">
                {{-- Guest Information --}}
                <flux:fieldset>
                    <flux:legend>Guest Information</flux:legend>

                    <flux:field class="sm:col-span-2">
                        <flux:label>Full Name</flux:label>
                        <flux:input wire:model="name" placeholder="Enter guest name" icon="user" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="guest@example.com"
                            icon="envelope" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Phone Number</flux:label>
                        <flux:input wire:model="phone" type="tel" placeholder="+1 (555) 123-4567"
                            icon="phone" />
                        <flux:error name="phone" />
                    </flux:field>
                </flux:fieldset>

                {{-- Reservation Details --}}
                <flux:fieldset>
                    <flux:legend>Reservation Details</flux:legend>

                    <flux:field>
                        <flux:label>Date</flux:label>
                        <flux:input wire:model="reservationDate" type="date" icon="calendar" />
                        <flux:error name="reservationDate" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Time</flux:label>
                        <flux:select wire:model="reservationTime" variant="listbox" placeholder="Select time"
                            icon="clock">
                            @foreach ($this->availableTimes() as $value => $label)
                                <flux:select.option value="{{ $value }}">{{ $label }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="reservationTime" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Party Size</flux:label>
                        <flux:input wire:model="partySize" type="number" min="1"
                            max="{{ App\Services\ReservationService::getMaxPartySize() }}" icon="users" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model="status" variant="listbox">
                            @foreach ($this->statuses() as $s)
                                <flux:select.option value="{{ $s->value }}">{{ $s->label() }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </flux:fieldset>

                {{-- Notes --}}
                <flux:field class="sm:col-span-2 mt-6">
                    <flux:label>Special Requests <span class="text-zinc-400">(optional)</span></flux:label>
                    <flux:textarea wire:model="notes" rows="3"
                        placeholder="Dietary restrictions, special occasions, accessibility needs..." />
                </flux:field>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button wire:click="$set('showModal', false)" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        {{ $editingId ? 'Update' : 'Create' }} Reservation
                    </span>
                    <span wire:loading>
                        <flux:icon name="arrow-path" class="animate-spin" />
                        Saving...
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
