<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new #[Title('Menu Manager')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterCategoryId = null;

    // Category form
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryDescription = '';

    // Item form
    public ?int $editingItemId = null;
    public int $itemCategoryId = 0;
    public string $itemName = '';
    public string $itemDescription = '';
    public string $itemPrice = '';
    public bool $itemIsAvailable = true;
    public bool $itemIsFeatured = false;
    public array $itemDietaryTags = [];

    public array $allDietaryTags = ['gluten-free', 'vegetarian', 'vegan', 'dairy-free', 'nut-free', 'spicy'];

    // Validation rules
    protected array $rules = [
        'categoryName' => 'required|string|min:2|max:100',
        'itemCategoryId' => 'required|integer|min:1|exists:menu_categories,id',
        'itemName' => 'required|string|min:2|max:150',
        'itemPrice' => 'required|numeric|min:0',
    ];

    #[Computed]
    public function categories(): mixed
    {
        return MenuCategory::active()->withCount('items')->orderBy('sort_order')->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategoryId(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function items(): mixed
    {
        return MenuItem::with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->filterCategoryId, fn ($q) => $q->where('menu_category_id', $this->filterCategoryId))
            ->orderBy('menu_category_id')
            ->orderBy('sort_order')
            ->paginate(15);
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
    }

    public function saveCategory(): void
    {
        $this->validate(['categoryName' => $this->rules['categoryName']]);

        $data = [
            'name' => $this->categoryName,
            'slug' => Str::slug($this->categoryName),
            'description' => $this->categoryDescription ?: null,
            'is_active' => true,
        ];

        try {
            if ($this->editingCategoryId) {
                MenuCategory::findOrFail($this->editingCategoryId)->update($data);
                $this->dispatch('category-updated', id: $this->editingCategoryId);
            } else {
                MenuCategory::create($data);
                $this->dispatch('category-created');
            }

            $this->resetCategoryForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save category: ' . $e->getMessage());
        }
    }

    public function deleteCategory(int $categoryId): void
    {
        try {
            $category = MenuCategory::findOrFail($categoryId);
            
            if ($category->items()->exists()) {
                session()->flash('error', 'Cannot delete category that contains menu items.');
                return;
            }
            
            $category->delete();
            $this->dispatch('category-deleted', id: $categoryId);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete category: ' . $e->getMessage());
        }
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
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemCategoryId' => $this->rules['itemCategoryId'],
            'itemName' => $this->rules['itemName'],
            'itemPrice' => $this->rules['itemPrice'],
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

        try {
            if ($this->editingItemId) {
                MenuItem::findOrFail($this->editingItemId)->update($data);
                $this->dispatch('item-updated', id: $this->editingItemId);
            } else {
                MenuItem::create($data);
                $this->dispatch('item-created');
            }

            $this->resetItemForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save item: ' . $e->getMessage());
        }
    }

    public function toggleAvailability(int $itemId): void
    {
        try {
            $item = MenuItem::findOrFail($itemId);
            $item->update(['is_available' => ! $item->is_available]);
            $this->dispatch('item-availability-toggled', id: $itemId, available: $item->is_available);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update availability: ' . $e->getMessage());
        }
    }

    public function deleteItem(int $itemId): void
    {
        try {
            MenuItem::findOrFail($itemId)->delete();
            $this->dispatch('item-deleted', id: $itemId);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete item: ' . $e->getMessage());
        }
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

        {{-- Toast Notifications --}}
        @if(session()->has('error'))
            <flux:toast variant="danger" icon="exclamation-triangle" dismissible>
                {{ session('error') }}
            </flux:toast>
        @endif

        {{-- Header --}}
        <flux:card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between p-6">
                <div>
                    <flux:heading size="xl">Menu Manager</flux:heading>
                    <flux:text class="mt-1">Manage categories and menu items</flux:text>
                </div>
                <flux:button.group>
                    <flux:modal.trigger name="category-modal">
                        <flux:button variant="outline" icon="plus">
                            Add Category
                        </flux:button>
                    </flux:modal.trigger>
                    <flux:modal.trigger name="item-modal">
                        <flux:button variant="primary" icon="plus">
                            Add Item
                        </flux:button>
                    </flux:modal.trigger>
                </flux:button.group>
            </div>
        </flux:card>

        {{-- Filters --}}
        <flux:card>
            <div class="p-4">
                <flux:heading size="md" class="mb-3">Filters</flux:heading>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <flux:input wire:model.live.debounce="search" placeholder="Search items..." icon="magnifying-glass" clearable class="sm:max-w-xs" />
                    <flux:select wire:model.live="filterCategoryId" variant="listbox" placeholder="All Categories" class="sm:max-w-xs">
                        @foreach($this->categories() as $category)
                        <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        </flux:card>

        {{-- Categories Summary --}}
        <flux:card>
            <flux:heading size="lg" class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">Categories ({{ $this->categories()->count() }})</flux:heading>
            
            <div class="p-5">
                <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach($this->categories() as $category)
                    <flux:card class="bg-zinc-50 dark:bg-zinc-800/50" wire:key="cat-{{ $category->id }}">
                        <div class="flex items-center justify-between p-4">
                            <div>
                                <flux:heading size="sm">{{ $category->name }}</flux:heading>
                                <flux:text size="xs" class="text-zinc-500">{{ $category->items_count }} items</flux:text>
                            </div>
                            <flux:button.group>
                                <flux:modal.trigger name="category-modal">
                                    <flux:button wire:click="openCategoryModal({{ $category->id }})" variant="ghost" size="sm" icon="pencil" />
                                </flux:modal.trigger>
                                <flux:button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Delete this category and all its items?" variant="ghost" size="sm" icon="trash" class="text-red-500" />
                            </flux:button.group>
                        </div>
                    </flux:card>
                    @endforeach
                </div>
            </div>
        </flux:card>

        {{-- Items Table --}}
        <flux:card>
            <flux:heading size="lg" class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">Menu Items ({{ $this->items->total() }})</flux:heading>

            <div class="p-5">
                <flux:table :paginate="$this->items">
                <flux:table.columns>
                    <flux:table.column>Item</flux:table.column>
                    <flux:table.column>Category</flux:table.column>
                    <flux:table.column>Price</flux:table.column>
                    <flux:table.column>Featured</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->items as $item)
                    <flux:table.row wire:key="item-{{ $item->id }}">
                        <flux:table.cell>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->name }}</p>
                            <p class="text-xs text-zinc-500 line-clamp-1">{{ $item->description }}</p>
                        </flux:table.cell>
                        <flux:table.cell>{{ $item->category->name }}</flux:table.cell>
                        <flux:table.cell variant="strong">${{ number_format($item->price, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($item->is_featured)
                            <flux:badge color="amber" size="sm">Featured</flux:badge>
                            @else
                            <span class="text-zinc-400 text-xs">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge 
                                wire:click="toggleAvailability({{ $item->id }})"
                                color="{{ $item->is_available ? 'green' : 'zinc' }}"
                                variant="{{ $item->is_available ? 'solid' : 'outline' }}"
                                class="cursor-pointer hover:opacity-80 transition-opacity"
                            >
                                {{ $item->is_available ? 'Available' : 'Unavailable' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end gap-1">
                                <flux:modal.trigger name="item-modal">
                                    <flux:button wire:click="openItemModal({{ $item->id }})" variant="ghost" size="sm" icon="pencil" />
                                </flux:modal.trigger>
                                <flux:button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this item?" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="py-12 text-center text-sm text-zinc-500">
                            No items found.
                        </flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            </div>
        </flux:card>

    </flux:main>

    {{-- Category Modal --}}
    <flux:modal name="category-modal" class="md:max-w-md">
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
                <flux:button close variant="ghost">Cancel</flux:button>
                <flux:button wire:click="saveCategory" variant="primary">
                    {{ $editingCategoryId ? 'Update' : 'Create' }} Category
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Item Modal --}}
    <flux:modal name="item-modal" class="md:max-w-lg">
        <div class="space-y-5">
            <flux:heading size="lg">{{ $editingItemId ? 'Edit Item' : 'New Menu Item' }}</flux:heading>

            <flux:field>
                <flux:label>Category</flux:label>
                <flux:select wire:model="itemCategoryId" variant="listbox" placeholder="Select a category...">
                    @foreach($this->categories() as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
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
                        <flux:checkbox 
                            wire:model="itemDietaryTags" 
                            value="{{ $tag }}"
                            label="{{ $tag }}"
                            class="text-xs"
                        />
                        @endforeach
                    </div>
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Description <span class="text-zinc-400">(optional)</span></flux:label>
                <flux:textarea wire:model="itemDescription" rows="2" placeholder="Describe the dish..." />
            </flux:field>

            <div class="space-y-4">
                <flux:field>
                    <flux:switch wire:model="itemIsAvailable" label="Available" description="Show this item on the menu" />
                </flux:field>
                <flux:field>
                    <flux:switch wire:model="itemIsFeatured" label="Featured" description="Show this item on the homepage" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button close variant="ghost">Cancel</flux:button>
                <flux:button wire:click="saveItem" variant="primary">
                    {{ $editingItemId ? 'Update' : 'Add' }} Item
                </flux:button>
            </div>
        </div>
    </flux:modal>

    </flux:main>
</div>
