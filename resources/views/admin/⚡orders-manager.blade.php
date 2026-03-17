<?php

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Enums\OrderStatus;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Orders')] class extends Component
{
    public string $filterStatus = '';

    // New order form
    public bool $showOrderModal = false;
    public ?int $editingOrderId = null;
    public int $selectedTableId = 0;
    public string $orderNotes = '';
    public array $orderLines = [];

    // Add item to order
    public int $addItemId = 0;
    public int $addQuantity = 1;
    public string $addItemNotes = '';

    public function orders(): mixed
    {
        return Order::with(['table', 'items'])
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when(! $this->filterStatus, fn ($q) => $q->active())
            ->orderByRaw("CASE status
                WHEN 'pending' THEN 1
                WHEN 'in_progress' THEN 2
                WHEN 'ready' THEN 3
                WHEN 'delivered' THEN 4
                ELSE 5 END")
            ->latest()
            ->get();
    }

    public function openOrderModal(?int $orderId = null): void
    {
        $this->resetOrderForm();

        if ($orderId) {
            $order = Order::with('items')->findOrFail($orderId);
            $this->editingOrderId = $orderId;
            $this->selectedTableId = $order->restaurant_table_id ?? 0;
            $this->orderNotes = $order->notes ?? '';
            $this->orderLines = $order->items->map(fn ($i) => [
                'id' => $i->id,
                'name' => $i->name,
                'price' => $i->price,
                'quantity' => $i->quantity,
                'notes' => $i->notes ?? '',
            ])->toArray();
        }

        $this->showOrderModal = true;
    }

    public function addLineItem(): void
    {
        if (! $this->addItemId) {
            return;
        }

        $menuItem = MenuItem::find($this->addItemId);

        if (! $menuItem) {
            return;
        }

        $this->orderLines[] = [
            'id' => null,
            'menu_item_id' => $menuItem->id,
            'name' => $menuItem->name,
            'price' => $menuItem->price,
            'quantity' => $this->addQuantity,
            'notes' => $this->addItemNotes,
        ];

        $this->addItemId = 0;
        $this->addQuantity = 1;
        $this->addItemNotes = '';
    }

    public function removeLineItem(int $index): void
    {
        unset($this->orderLines[$index]);
        $this->orderLines = array_values($this->orderLines);
    }

    public function saveOrder(): void
    {
        $this->validate([
            'selectedTableId' => ['required', 'integer', 'min:1', 'exists:restaurant_tables,id'],
        ]);

        $subtotal = collect($this->orderLines)->sum(fn ($l) => $l['price'] * $l['quantity']);
        $tax = $subtotal * 0.08;

        if ($this->editingOrderId) {
            $order = Order::findOrFail($this->editingOrderId);
            $order->update([
                'restaurant_table_id' => $this->selectedTableId,
                'notes' => $this->orderNotes ?: null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);

            $order->items()->delete();
        } else {
            $order = Order::create([
                'restaurant_table_id' => $this->selectedTableId,
                'status' => OrderStatus::Pending,
                'notes' => $this->orderNotes ?: null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);
        }

        foreach ($this->orderLines as $line) {
            $order->items()->create([
                'menu_item_id' => $line['menu_item_id'] ?? null,
                'name' => $line['name'],
                'price' => $line['price'],
                'quantity' => $line['quantity'],
                'notes' => $line['notes'] ?: null,
            ]);
        }

        $this->showOrderModal = false;
        $this->resetOrderForm();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        Order::findOrFail($orderId)->update(['status' => $status]);
    }

    public function deleteOrder(int $orderId): void
    {
        Order::findOrFail($orderId)->delete();
    }

    public function orderTotal(): float
    {
        $subtotal = collect($this->orderLines)->sum(fn ($l) => $l['price'] * $l['quantity']);
        return $subtotal + ($subtotal * 0.08);
    }

    private function resetOrderForm(): void
    {
        $this->editingOrderId = null;
        $this->selectedTableId = 0;
        $this->orderNotes = '';
        $this->orderLines = [];
        $this->addItemId = 0;
        $this->addQuantity = 1;
        $this->addItemNotes = '';
    }

    public function statuses(): array
    {
        return OrderStatus::cases();
    }

    public function tables(): mixed
    {
        return RestaurantTable::orderBy('number')->get();
    }

    public function menuItems(): mixed
    {
        return MenuItem::available()->with('category')->orderBy('menu_category_id')->orderBy('sort_order')->get();
    }
};
?>

<div>
    <flux:main class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Orders</flux:heading>
                <flux:text class="mt-1">Kitchen queue and table orders</flux:text>
            </div>
            <flux:button wire:click="openOrderModal" variant="primary" icon="plus">
                New Order
            </flux:button>
        </div>

        {{-- Status Filter --}}
        <div class="flex gap-2 flex-wrap">
            <button wire:click="$set('filterStatus', '')"
                    class="rounded-full px-4 py-1.5 text-xs font-medium transition-colors {{ !$filterStatus ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                Active Orders
            </button>
            @foreach($this->statuses() as $s)
            <button wire:click="$set('filterStatus', '{{ $s->value }}')"
                    class="rounded-full px-4 py-1.5 text-xs font-medium transition-colors {{ $filterStatus === $s->value ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                {{ $s->label() }}
            </button>
            @endforeach
        </div>

        {{-- Orders Grid --}}
        @if($this->orders()->isEmpty())
        <div class="rounded-xl border border-zinc-200 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-400">No orders found.</flux:text>
        </div>
        @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->orders() as $order)
            @php
                $borderColor = match($order->status) {
                    \App\Enums\OrderStatus::Pending => 'border-yellow-200 dark:border-yellow-800/40',
                    \App\Enums\OrderStatus::InProgress => 'border-blue-200 dark:border-blue-800/40',
                    \App\Enums\OrderStatus::Ready => 'border-green-200 dark:border-green-800/40',
                    \App\Enums\OrderStatus::Delivered => 'border-zinc-200 dark:border-zinc-700',
                    \App\Enums\OrderStatus::Cancelled => 'border-red-200 dark:border-red-800/40',
                };
            @endphp
            <div class="rounded-xl border-2 bg-white p-5 dark:bg-zinc-900 {{ $borderColor }}" wire:key="order-{{ $order->id }}">
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $order->table?->display_name ?? 'No Table' }}</p>
                        <p class="text-xs text-zinc-500">Order #{{ $order->id }}</p>
                    </div>
                    <div class="flex gap-1">
                        <flux:button wire:click="openOrderModal({{ $order->id }})" variant="ghost" size="xs" icon="pencil" />
                        <flux:button wire:click="deleteOrder({{ $order->id }})" wire:confirm="Delete this order?" variant="ghost" size="xs" icon="trash" class="text-red-500" />
                    </div>
                </div>

                {{-- Items --}}
                <div class="mb-3 space-y-1">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ $item->quantity }}x {{ $item->name }}</span>
                        <span class="text-zinc-500">${{ number_format($item->price * $item->quantity, 2) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mb-3 border-t border-zinc-100 pt-2 dark:border-zinc-800">
                    <div class="flex justify-between text-sm font-semibold">
                        <span class="text-zinc-900 dark:text-zinc-100">Total</span>
                        <span class="text-zinc-900 dark:text-zinc-100">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                <flux:select wire:change="updateStatus({{ $order->id }}, $event.target.value)" class="w-full text-xs">
                    @foreach($this->statuses() as $s)
                    <option value="{{ $s->value }}" @selected($order->status === $s)>{{ $s->label() }}</option>
                    @endforeach
                </flux:select>
            </div>
            @endforeach
        </div>
        @endif

    </flux:main>

    {{-- Order Modal --}}
    <flux:modal wire:model="showOrderModal" class="md:max-w-2xl">
        <div class="space-y-5">
            <flux:heading size="lg">{{ $editingOrderId ? 'Edit Order' : 'New Order' }}</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Table</flux:label>
                    <flux:select wire:model="selectedTableId">
                        <option value="0">Select a table...</option>
                        @foreach($this->tables() as $table)
                        <option value="{{ $table->id }}">{{ $table->display_name }} ({{ $table->capacity }} seats)</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="selectedTableId" />
                </flux:field>

                <flux:field>
                    <flux:label>Notes <span class="text-zinc-400">(optional)</span></flux:label>
                    <flux:input wire:model="orderNotes" placeholder="Special instructions..." />
                </flux:field>
            </div>

            {{-- Add Items --}}
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Add Items</p>
                <div class="flex gap-2">
                    <flux:select wire:model="addItemId" class="flex-1">
                        <option value="0">Select menu item...</option>
                        @foreach($this->menuItems()->groupBy(fn ($i) => $i->category->name) as $catName => $items)
                        <optgroup label="{{ $catName }}">
                            @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} — ${{ number_format($item->price, 2) }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="addQuantity" type="number" min="1" class="w-16" />
                    <flux:button wire:click="addLineItem" variant="outline" icon="plus">Add</flux:button>
                </div>
            </div>

            {{-- Order Lines --}}
            @if(count($orderLines) > 0)
            <div class="divide-y divide-zinc-100 rounded-xl border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-700">
                @foreach($orderLines as $index => $line)
                <div class="flex items-center justify-between px-4 py-3" wire:key="line-{{ $index }}">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $line['quantity'] }}x {{ $line['name'] }}</p>
                        <p class="text-xs text-zinc-500">${{ number_format($line['price'], 2) }} each</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">${{ number_format($line['price'] * $line['quantity'], 2) }}</p>
                        <flux:button wire:click="removeLineItem({{ $index }})" variant="ghost" size="xs" icon="x-mark" class="text-red-500" />
                    </div>
                </div>
                @endforeach
                <div class="flex justify-between px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50">
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Total (incl. tax)</p>
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($this->orderTotal(), 2) }}</p>
                </div>
            </div>
            @endif

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showOrderModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="saveOrder" variant="primary">
                    {{ $editingOrderId ? 'Update' : 'Create' }} Order
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
