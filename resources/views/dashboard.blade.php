<x-layouts::app :title="__('Dashboard')">
    @php
        $todayReservations = \App\Models\Reservation::today()->count();
        $pendingReservations = \App\Models\Reservation::where('status', \App\Enums\ReservationStatus::Pending->value)->count();
        $activeOrders = \App\Models\Order::active()->count();
        $availableTables = \App\Models\RestaurantTable::available()->count();
        $totalTables = \App\Models\RestaurantTable::count();
        $todayRevenue = \App\Models\Order::where('status', \App\Enums\OrderStatus::Delivered->value)
            ->whereDate('created_at', today())->sum('total');
        $upcomingReservations = \App\Models\Reservation::upcoming()->take(6)->get();
        $recentOrders = \App\Models\Order::with('table')->active()->latest()->take(5)->get();
    @endphp

    <flux:main class="space-y-6">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Dashboard</flux:heading>
                <flux:text class="mt-1">{{ now()->format('l, F j, Y') }}</flux:text>
            </div>
            <flux:button :href="route('admin.reservations')" wire:navigate variant="primary" icon="plus">
                New Reservation
            </flux:button>
        </div>

        {{-- Stats Grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <flux:text class="text-xs font-semibold uppercase tracking-widest">Today's Bookings</flux:text>
                    <div class="rounded-lg bg-blue-50 p-2 dark:bg-blue-900/20">
                        <flux:icon.calendar-days class="size-4 text-blue-500" />
                    </div>
                </div>
                <p class="mt-3 font-serif text-3xl font-bold text-zinc-900 dark:text-zinc-50">{{ $todayReservations }}</p>
                @if($pendingReservations > 0)
                <p class="mt-1 text-xs text-amber-500">{{ $pendingReservations }} pending confirmation</p>
                @else
                <p class="mt-1 text-xs text-zinc-500">All confirmed</p>
                @endif
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <flux:text class="text-xs font-semibold uppercase tracking-widest">Active Orders</flux:text>
                    <div class="rounded-lg bg-amber-50 p-2 dark:bg-amber-900/20">
                        <flux:icon.shopping-bag class="size-4 text-amber-500" />
                    </div>
                </div>
                <p class="mt-3 font-serif text-3xl font-bold text-zinc-900 dark:text-zinc-50">{{ $activeOrders }}</p>
                <p class="mt-1 text-xs text-zinc-500">In kitchen or ready</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <flux:text class="text-xs font-semibold uppercase tracking-widest">Tables</flux:text>
                    <div class="rounded-lg bg-green-50 p-2 dark:bg-green-900/20">
                        <flux:icon.table-cells class="size-4 text-green-500" />
                    </div>
                </div>
                <p class="mt-3 font-serif text-3xl font-bold text-zinc-900 dark:text-zinc-50">{{ $availableTables }}<span class="text-lg text-zinc-400">/{{ $totalTables }}</span></p>
                <p class="mt-1 text-xs text-zinc-500">Available right now</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <flux:text class="text-xs font-semibold uppercase tracking-widest">Today's Revenue</flux:text>
                    <div class="rounded-lg bg-emerald-50 p-2 dark:bg-emerald-900/20">
                        <flux:icon.currency-dollar class="size-4 text-emerald-500" />
                    </div>
                </div>
                <p class="mt-3 font-serif text-3xl font-bold text-zinc-900 dark:text-zinc-50">${{ number_format($todayRevenue, 0) }}</p>
                <p class="mt-1 text-xs text-zinc-500">From delivered orders</p>
            </div>
        </div>

        {{-- Two-column layout --}}
        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Upcoming Reservations --}}
            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                    <flux:heading size="lg">Upcoming Reservations</flux:heading>
                    <flux:button :href="route('admin.reservations')" wire:navigate variant="ghost" size="sm">
                        View all
                    </flux:button>
                </div>

                @if($upcomingReservations->isEmpty())
                <div class="p-8 text-center">
                    <flux:text class="text-zinc-400">No upcoming reservations</flux:text>
                </div>
                @else
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($upcomingReservations as $reservation)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $reservation->name }}</p>
                            <p class="text-xs text-zinc-500">
                                {{ $reservation->reservation_date->format('M j') }} at {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A') }}
                                · {{ $reservation->party_size }} guests
                            </p>
                        </div>
                        <flux:badge :color="$reservation->status->color()" size="sm">
                            {{ $reservation->status->label() }}
                        </flux:badge>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Active Orders --}}
            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                    <flux:heading size="lg">Active Orders</flux:heading>
                    <flux:button :href="route('admin.orders')" wire:navigate variant="ghost" size="sm">
                        View all
                    </flux:button>
                </div>

                @if($recentOrders->isEmpty())
                <div class="p-8 text-center">
                    <flux:text class="text-zinc-400">No active orders</flux:text>
                </div>
                @else
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $order->table?->display_name ?? 'Table —' }}
                            </p>
                            <p class="text-xs text-zinc-500">Order #{{ $order->id }} · ${{ number_format($order->total, 2) }}</p>
                        </div>
                        <flux:badge :color="$order->status->color()" size="sm">
                            {{ $order->status->label() }}
                        </flux:badge>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>

        {{-- Quick Links --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('admin.reservations') }}" wire:navigate
               class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800">
                <div class="rounded-lg bg-blue-50 p-2 dark:bg-blue-900/20">
                    <flux:icon.clipboard-document-list class="size-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Reservations</p>
                    <p class="text-xs text-zinc-500">Manage bookings</p>
                </div>
            </a>
            <a href="{{ route('admin.tables') }}" wire:navigate
               class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800">
                <div class="rounded-lg bg-green-50 p-2 dark:bg-green-900/20">
                    <flux:icon.table-cells class="size-5 text-green-500" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Tables</p>
                    <p class="text-xs text-zinc-500">Floor status</p>
                </div>
            </a>
            <a href="{{ route('admin.orders') }}" wire:navigate
               class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800">
                <div class="rounded-lg bg-amber-50 p-2 dark:bg-amber-900/20">
                    <flux:icon.shopping-bag class="size-5 text-amber-500" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Orders</p>
                    <p class="text-xs text-zinc-500">Kitchen queue</p>
                </div>
            </a>
            <a href="{{ route('admin.menu') }}" wire:navigate
               class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800">
                <div class="rounded-lg bg-purple-50 p-2 dark:bg-purple-900/20">
                    <flux:icon.bookmark-square class="size-5 text-purple-500" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Menu</p>
                    <p class="text-xs text-zinc-500">Items & categories</p>
                </div>
            </a>
        </div>
    </flux:main>
</x-layouts::app>
