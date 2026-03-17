<?php

use App\Models\RestaurantTable;
use App\Enums\TableStatus;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tables')] class extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;
    public int $number = 0;
    public string $name = '';
    public int $capacity = 4;
    public string $section = 'main';
    public string $status = '';

    public function mount(): void
    {
        $this->status = TableStatus::Available->value;
    }

    public function tables(): mixed
    {
        return RestaurantTable::orderBy('section')->orderBy('number')->get();
    }

    public function tablesBySection(): mixed
    {
        return $this->tables()->groupBy('section');
    }

    public function openModal(?int $id = null): void
    {
        $this->resetForm();

        if ($id) {
            $table = RestaurantTable::findOrFail($id);
            $this->editingId = $id;
            $this->number = $table->number;
            $this->name = $table->name ?? '';
            $this->capacity = $table->capacity;
            $this->section = $table->section;
            $this->status = $table->status->value;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'number' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'section' => ['required', 'string'],
            'status' => ['required'],
        ]);

        $data = [
            'number' => $this->number,
            'name' => $this->name ?: null,
            'capacity' => $this->capacity,
            'section' => $this->section,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            RestaurantTable::findOrFail($this->editingId)->update($data);
        } else {
            RestaurantTable::create($data);
        }

        $this->showModal = false;
    }

    public function updateStatus(int $id, string $status): void
    {
        RestaurantTable::findOrFail($id)->update(['status' => $status]);
    }

    public function delete(int $id): void
    {
        RestaurantTable::findOrFail($id)->delete();
    }

    public function statuses(): array
    {
        return TableStatus::cases();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->number = 0;
        $this->name = '';
        $this->capacity = 4;
        $this->section = 'main';
        $this->status = TableStatus::Available->value;
    }
};
?>

<div>
    <flux:main class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Tables</flux:heading>
                <flux:text class="mt-1">Floor map and table management</flux:text>
            </div>
            <flux:button wire:click="openModal" variant="primary" icon="plus">
                Add Table
            </flux:button>
        </div>

        {{-- Status Summary --}}
        <div class="grid gap-4 sm:grid-cols-4">
            @foreach(\App\Enums\TableStatus::cases() as $s)
            @php $count = $this->tables()->where('status', $s)->count(); @endphp
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">{{ $count }}</p>
                <p class="text-xs text-zinc-500">{{ $s->label() }}</p>
            </div>
            @endforeach
        </div>

        {{-- Floor Map by Section --}}
        @foreach($this->tablesBySection() as $section => $tables)
        <div>
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ ucfirst($section) }} Section</h3>
            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                @foreach($tables as $table)
                @php
                    $bgColor = match($table->status) {
                        \App\Enums\TableStatus::Available => 'border-green-200 bg-green-50 dark:border-green-800/40 dark:bg-green-900/20',
                        \App\Enums\TableStatus::Occupied => 'border-red-200 bg-red-50 dark:border-red-800/40 dark:bg-red-900/20',
                        \App\Enums\TableStatus::Reserved => 'border-amber-200 bg-amber-50 dark:border-amber-800/40 dark:bg-amber-900/20',
                        \App\Enums\TableStatus::Maintenance => 'border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800',
                    };
                    $dotColor = match($table->status) {
                        \App\Enums\TableStatus::Available => 'bg-green-500',
                        \App\Enums\TableStatus::Occupied => 'bg-red-500',
                        \App\Enums\TableStatus::Reserved => 'bg-amber-500',
                        \App\Enums\TableStatus::Maintenance => 'bg-zinc-400',
                    };
                @endphp
                <div class="rounded-xl border p-4 {{ $bgColor }}" wire:key="table-{{ $table->id }}">
                    <div class="mb-2 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full {{ $dotColor }}"></span>
                            <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ $table->display_name }}
                            </span>
                        </div>
                        <div class="flex gap-0.5">
                            <flux:button wire:click="openModal({{ $table->id }})" variant="ghost" size="xs" icon="pencil" />
                            <flux:button wire:click="delete({{ $table->id }})" wire:confirm="Delete this table?" variant="ghost" size="xs" icon="trash" class="text-red-500" />
                        </div>
                    </div>
                    <p class="mb-2 text-xs text-zinc-500">{{ $table->capacity }} seats</p>
                    <flux:select wire:change="updateStatus({{ $table->id }}, $event.target.value)"
                                 class="text-xs !py-1 !px-2 w-full">
                        @foreach($this->statuses() as $s)
                        <option value="{{ $s->value }}" @selected($table->status === $s)>{{ $s->label() }}</option>
                        @endforeach
                    </flux:select>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

    </flux:main>

    {{-- Table Modal --}}
    <flux:modal wire:model="showModal" class="md:max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg">{{ $editingId ? 'Edit Table' : 'Add Table' }}</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Table Number</flux:label>
                    <flux:input wire:model="number" type="number" min="1" placeholder="e.g. 12" />
                    <flux:error name="number" />
                </flux:field>

                <flux:field>
                    <flux:label>Name <span class="text-zinc-400">(optional)</span></flux:label>
                    <flux:input wire:model="name" placeholder="e.g. Private Room" />
                </flux:field>

                <flux:field>
                    <flux:label>Capacity (seats)</flux:label>
                    <flux:input wire:model="capacity" type="number" min="1" max="50" />
                    <flux:error name="capacity" />
                </flux:field>

                <flux:field>
                    <flux:label>Section</flux:label>
                    <flux:select wire:model="section">
                        <option value="main">Main Dining</option>
                        <option value="patio">Patio</option>
                        <option value="bar">Bar</option>
                        <option value="private">Private</option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        @foreach($this->statuses() as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="save" variant="primary">
                    {{ $editingId ? 'Update' : 'Add' }} Table
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
