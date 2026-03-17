<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Starters',
                'description' => 'Begin your evening with our hand-crafted appetizers',
                'sort_order' => 1,
                'items' => [
                    ['name' => 'Wagyu Beef Tartare', 'description' => 'Hand-cut A5 wagyu, quail egg, truffle aioli, brioche crostini', 'price' => 24.00, 'is_featured' => true, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Seared Scallops', 'description' => 'Day-boat scallops, roasted cauliflower purée, pancetta crisp, micro greens', 'price' => 22.00, 'is_featured' => true, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Charcuterie Board', 'description' => 'Chef\'s selection of cured meats, artisan cheeses, seasonal accompaniments', 'price' => 28.00, 'dietary_tags' => [], 'image_url' => 'https://images.unsplash.com/photo-1504973960431-1c467e159aa4?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Burrata Caprese', 'description' => 'Heirloom tomatoes, fresh burrata, aged balsamic, basil oil', 'price' => 18.00, 'dietary_tags' => ['vegetarian', 'gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Shrimp Cocktail', 'description' => 'Gulf shrimp, house-made cocktail sauce, lemon', 'price' => 19.00, 'dietary_tags' => ['gluten-free', 'dairy-free'], 'image_url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
            [
                'name' => 'Soups & Salads',
                'description' => 'Fresh, seasonal offerings crafted daily',
                'sort_order' => 2,
                'items' => [
                    ['name' => 'French Onion Soup', 'description' => 'Slow-caramelized onions, rich beef broth, Gruyère croûte', 'price' => 14.00, 'dietary_tags' => ['vegetarian'], 'image_url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Lobster Bisque', 'description' => 'Maine lobster, cognac cream, chive oil, brioche croutons', 'price' => 18.00, 'is_featured' => true, 'dietary_tags' => [], 'image_url' => 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Caesar Salad', 'description' => 'Romaine hearts, house dressing, shaved Parmigiano, house-baked croutons', 'price' => 15.00, 'dietary_tags' => ['vegetarian'], 'image_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Warm Beet Salad', 'description' => 'Roasted beets, whipped goat cheese, candied walnuts, arugula, citrus vinaigrette', 'price' => 16.00, 'dietary_tags' => ['vegetarian', 'gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
            [
                'name' => 'Pasta',
                'description' => 'House-made pasta crafted fresh each morning',
                'sort_order' => 3,
                'items' => [
                    ['name' => 'Truffle Fettuccine', 'description' => 'House-made pasta, black truffle, Parmigiano cream, chives', 'price' => 32.00, 'is_featured' => true, 'dietary_tags' => ['vegetarian'], 'image_url' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Lobster Linguine', 'description' => 'Maine lobster, cherry tomatoes, white wine, garlic, fresh herbs', 'price' => 42.00, 'dietary_tags' => ['dairy-free'], 'image_url' => 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Mushroom Risotto', 'description' => 'Wild mushroom blend, Arborio rice, Parmigiano, truffle oil', 'price' => 28.00, 'dietary_tags' => ['vegetarian', 'gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
            [
                'name' => 'Steaks & Chops',
                'description' => 'Prime cuts, aged to perfection',
                'sort_order' => 4,
                'items' => [
                    ['name' => 'Filet Mignon 8oz', 'description' => 'USDA Prime center cut, roasted garlic, red wine demi-glace', 'price' => 62.00, 'is_featured' => true, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1546039907-7fa05f864c02?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Bone-In Ribeye 20oz', 'description' => '28-day dry-aged, compound butter, roasted shallots', 'price' => 78.00, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'New York Strip 12oz', 'description' => 'USDA Prime, herb crust, bordelaise sauce', 'price' => 58.00, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1529694157872-4e0c0f3b238b?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Rack of Lamb', 'description' => 'French-trimmed Colorado lamb, mint chimichurri, roasted fingerlings', 'price' => 56.00, 'is_featured' => true, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1484980972926-edee96e0960d?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
            [
                'name' => 'Seafood',
                'description' => 'Sustainably sourced, ocean-fresh daily',
                'sort_order' => 5,
                'items' => [
                    ['name' => 'Pan-Seared Salmon', 'description' => 'Wild King salmon, lemon beurre blanc, asparagus, wild rice', 'price' => 38.00, 'is_featured' => true, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Whole Branzino', 'description' => 'Mediterranean sea bass, capers, olives, roasted tomatoes, herb oil', 'price' => 44.00, 'dietary_tags' => ['gluten-free', 'dairy-free'], 'image_url' => 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Butter-Poached Lobster', 'description' => 'Whole Maine lobster, drawn butter, seasonal vegetables', 'price' => 72.00, 'dietary_tags' => ['gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1559742811-822873691df8?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
            [
                'name' => 'Sides',
                'description' => 'Crafted accompaniments for the table',
                'sort_order' => 6,
                'items' => [
                    ['name' => 'Truffle Mac & Cheese', 'description' => 'Cavatappi, black truffle, Gruyère, Parmigiano, breadcrumb crust', 'price' => 14.00, 'dietary_tags' => ['vegetarian'], 'image_url' => 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Garlic Mashed Potatoes', 'description' => 'Yukon Gold, roasted garlic, crème fraîche, chives', 'price' => 10.00, 'dietary_tags' => ['vegetarian', 'gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Roasted Asparagus', 'description' => 'Lemon zest, shaved Parmigiano, toasted almonds', 'price' => 11.00, 'dietary_tags' => ['vegetarian', 'gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1584255014406-2a68ea38e48c?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'House-Cut Fries', 'description' => 'Double-fried, sea salt, house aioli', 'price' => 9.00, 'dietary_tags' => ['vegetarian', 'gluten-free', 'dairy-free'], 'image_url' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
            [
                'name' => 'Desserts',
                'description' => 'A sweet conclusion to your evening',
                'sort_order' => 7,
                'items' => [
                    ['name' => 'Chocolate Lava Cake', 'description' => 'Valrhona dark chocolate, vanilla bean ice cream, raspberry coulis', 'price' => 14.00, 'is_featured' => true, 'dietary_tags' => ['vegetarian'], 'image_url' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Crème Brûlée', 'description' => 'Classic vanilla bean custard, caramelized sugar, fresh berries', 'price' => 12.00, 'dietary_tags' => ['vegetarian', 'gluten-free'], 'image_url' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Tiramisu', 'description' => 'House-made, espresso-soaked ladyfingers, mascarpone cream, cocoa', 'price' => 13.00, 'dietary_tags' => ['vegetarian'], 'image_url' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'New York Cheesecake', 'description' => 'Graham cracker crust, seasonal berry compote, whipped cream', 'price' => 12.00, 'dietary_tags' => ['vegetarian'], 'image_url' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $items = $categoryData['items'];
            unset($categoryData['items']);

            $category = MenuCategory::create([
                ...$categoryData,
                'slug' => Str::slug($categoryData['name']),
                'is_active' => true,
            ]);

            foreach ($items as $index => $itemData) {
                MenuItem::create([
                    'menu_category_id' => $category->id,
                    'slug' => Str::slug($itemData['name']),
                    'is_available' => true,
                    'sort_order' => $index,
                    'is_featured' => $itemData['is_featured'] ?? false,
                    ...$itemData,
                ]);
            }
        }
    }
}
