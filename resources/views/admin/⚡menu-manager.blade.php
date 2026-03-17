<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;

new #[Title('Menu Manager')] class extends Component
{
    public string $search = '';
    public ?int $filterCategoryId = null;

    // Category form
    public bool $showCategoryModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryDescription = '';

    // Item form
    public bool $showItemModal = false;
    public ?int $editingItemId = null;
    public int $itemCategoryId = 0;
    public string $itemName = '';
    public string $itemDescription = '';
    public string $itemPrice = '';
    public bool $itemIsAvailable = true;
    public bool $itemIsFeatured = false;
    public array $itemDietaryTags = [];

    public array $allDietaryTags = ['gluten-free', 'vegetarian', 'vegan', 'dairy-free', 'nut-free', 'spicy'];

    public function categories(): mixed
    {
        return MenuCategory::active()->withCount('items')->get();
    }

    public function items(): mixed
    {
        return MenuItem::with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterCategoryId, fn ($q) => $q->where('menu_category_id', $this->filterCategoryId))
            ->orderBy('menu_category_id')
            ->orderBy('sort_order')
            ->get();
    }

    public function openCategoryModal(?int $categoryId = null): void
    {
        $this->resetCategoryForm();

        if ($categoryId) {
            $category = MenuCategory::findOrFail($categoryId);
            $this->editingCategoryId = $categoryId;
            $this->categoryName = $category->name;
            $this->categoryDescription = $category->description ?? '';
        }

        $this->showCategoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $data = [
            'name' => $this->categoryName,
            'slug' => Str::slug($this->categoryName),
            'description' => $this->categoryDescription ?: null,
            'is_active' => true,
        ];

        if ($this->editingCategoryId) {
            MenuCategory::findOrFail($this->editingCategoryId)->update($data);
        } else {
            MenuCategory::create($data);
        }

        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    public function deleteCategory(int $categoryId): void
    {
        MenuCategory::findOrFail($categoryId)->delete();
    }

    public function openItemModal(?int $itemId = null): void
    {
        $this->resetItemForm();

        if ($itemId) {
            $item = MenuItem::findOrFail($itemId);
            $this->editingItemId = $itemId;
            $this->itemCategoryId = $item->menu_category_id;
            $this->itemName = $item->name;
            $this->itemDescription = $item->description ?? '';
            $this->itemPrice = (string) $item->price;
            $this->itemIsAvailable = $item->is_available;
            $this->itemIsFeatured = $item->is_featured;
            $this->itemDietaryTags = $item->dietary_tags ?? [];
        }

        $this->showItemModal = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemCategoryId' => ['required', 'integer', 'min:1', 'exists:menu_categories,id'],
            'itemName' => ['required', 'string', 'min:2', 'max:150'],
            'itemPrice' => ['required', 'numeric', 'min:0'],
        ]);

        $data = [
            'menu_category_id' => $this->itemCategoryId,
            'name' => $this->itemName,
            'slug' => Str::slug($this->itemName),
            'description' => $this->itemDescription ?: null,
            'price' => $this->itemPrice,
            'is_available' => $this->itemIsAvailable,
            'is_featured' => $this->itemIsFeatured,
            'dietary_tags' => $this->itemDietaryTags ?: null,
        ];

        if ($this->editingItemId) {
            MenuItem::findOrFail($this->editingItemId)->update($data);
        } else {
            MenuItem::create($data);
        }

        $this->showItemModal = false;
        $this->resetItemForm();
    }

    public function toggleAvailability(int $itemId): void
    {
        $item = MenuItem::findOrFail($itemId);
        $item->update(['is_available' => ! $item->is_available]);
    }

    public function deleteItem(int $itemId): void
    {
        MenuItem::findOrFail($itemId)->delete();
    }

    private function resetCategoryForm(): void
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categoryDescription = '';
    }

    private function resetItemForm(): void
    {
        $this->editingItemId = null;
        $this->itemCategoryId = 0;
        $this->itemName = '';
        $this->itemDescription = '';
        $this->itemPrice = '';
        $this->itemIsAvailable = true;
        $this->itemIsFeatured = false;
        $this->itemDietaryTags = [];
    }
};
?>

<div>
    <flux:main class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Menu Manager</flux:heading>
                <flux:text class="mt-1">Manage categories and menu items</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="openCategoryModal" variant="outline" icon="plus">
                    Add Category
                </flux:button>
                <flux:button wire:click="openItemModal" variant="primary" icon="plus">
                    Add Item
                </flux:button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col gap-3 sm:flex-row">
            <flux:input wire:model.live.debounce="search" placeholder="Search items..." icon="magnifying-glass" class="sm:max-w-xs" />
            <flux:select wire:model.live="filterCategoryId" class="sm:max-w-xs">
                <option value="">All Categories</option>
                @foreach($this->categories() as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </flux:select>
        </div>

        {{-- Categories Summary --}}
        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($this->categories() as $category)
            <div class="flex items-center justify-between rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900" wire:key="cat-{{ $category->id }}">
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $category->name }}</p>
                    <p class="text-xs text-zinc-500">{{ $category->items_count }} items</p>
                </div>
                <div class="flex gap-1">
                    <flux:button wire:click="openCategoryModal({{ $category->id }})" variant="ghost" size="sm" icon="pencil" />
                    <flux:button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Delete this category?" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                </div>
            </div>
            @endforeach
        </div>

        {{-- Items Table --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                <flux:heading size="lg">Menu Items ({{ $this->items()->count() }})</flux:heading>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Item</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Category</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Price</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($this->items() as $item)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50" wire:key="item-{{ $item->id }}">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->name }}</p>
                                <p class="text-xs text-zinc-500 line-clamp-1">{{ $item->description }}</p>
                                @if($item->is_featured)
                                <span class="mt-1 inline-block rounded-full bg-amber-100 px-1.5 py-0.5 text-xs text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Featured</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">{{ $item->category->name }}</td>
                            <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">${{ number_format($item->price, 2) }}</td>
                            <td class="px-5 py-3.5">
                                <button wire:click="toggleAvailability({{ $item->id }})"
                                        class="inline-flex cursor-pointer items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium transition-colors
                                        {{ $item->is_available ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                    <span class="size-1.5 rounded-full {{ $item->is_available ? 'bg-green-500' : 'bg-zinc-400' }}"></span>
                                    {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                </button>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-end gap-1">
                                    <flux:button wire:click="openItemModal({{ $item->id }})" variant="ghost" size="sm" icon="pencil" />
                                    <flux:button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this item?" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-zinc-500">
                                No items found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </flux:main>

    {{-- Category Modal --}}
    <flux:modal wire:model="showCategoryModal" class="md:max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg">{{ $editingCategoryId ? 'Edit Category' : 'New Category' }}</flux:heading>

            <flux:field>
                <flux:label>Category Name</flux:label>
                <flux:input wire:model="categoryName" placeholder="e.g. Starters" />
                <flux:error name="categoryName" />
            </flux:field>

            <flux:field>
                <flux:label>Description <span class="text-zinc-400">(optional)</span></flux:label>
                <flux:textarea wire:model="categoryDescription" rows="2" placeholder="Brief description..." />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showCategoryModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="saveCategory" variant="primary">
                    {{ $editingCategoryId ? 'Update' : 'Create' }} Category
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Item Modal --}}
    <flux:modal wire:model="showItemModal" class="md:max-w-lg">
        <div class="space-y-5">
            <flux:heading size="lg">{{ $editingItemId ? 'Edit Item' : 'New Menu Item' }}</flux:heading>

            <flux:field>
                <flux:label>Category</flux:label>
                <flux:select wire:model="itemCategoryId">
                    <option value="0">Select a category...</option>
                    @foreach($this->categories() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="itemCategoryId" />
            </flux:field>

            <flux:field>
                <flux:label>Item Name</flux:label>
                <flux:input wire:model="itemName" placeholder="e.g. Filet Mignon" />
                <flux:error name="itemName" />
            </flux:field>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Price ($)</flux:label>
                    <flux:input wire:model="itemPrice" type="number" step="0.01" min="0" placeholder="0.00" />
                    <flux:error name="itemPrice" />
                </flux:field>

                <flux:field>
                    <flux:label>Dietary Tags</flux:label>
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($allDietaryTags as $tag)
                        <label class="flex cursor-pointer items-center gap-1.5">
                            <input type="checkbox" wire:model="itemDietaryTags" value="{{ $tag }}"
                                   class="rounded border-zinc-300 text-zinc-900 dark:border-zinc-600" />
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ $tag }}</span>
                        </label>
                        @endforeach
                    </div>
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Description <span class="text-zinc-400">(optional)</span></flux:label>
                <flux:textarea wire:model="itemDescription" rows="2" placeholder="Describe the dish..." />
            </flux:field>

            <div class="flex gap-6">
                <flux:switch wire:model="itemIsAvailable" label="Available" />
                <flux:switch wire:model="itemIsFeatured" label="Featured on homepage" />
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('showItemModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="saveItem" variant="primary">
                    {{ $editingItemId ? 'Update' : 'Add' }} Item
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
