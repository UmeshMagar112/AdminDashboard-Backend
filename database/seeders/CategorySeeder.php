<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Basic top‑level categories
        $categories = [
            'Electronics' => [
                'Mobiles',
                'Laptops',
                'Accessories',
            ],
            'Fashion' => [
                'Men',
                'Women',
            ],
            'Home & Kitchen' => [
                'Furniture',
                'Appliances',
            ],
        ];

        foreach ($categories as $parentName => $children) {
            $parent = Category::firstOrCreate(
                ['name' => $parentName],
                [
                    'description' => $parentName . ' category',
                    'status'      => 1,
                    'sort_order'  => 0,
                ],
            );

            foreach ($children as $index => $childName) {
                Category::firstOrCreate(
                    ['name' => $childName, 'parent_id' => $parent->id],
                    [
                        'description' => $childName . ' category',
                        'status'      => 1,
                        'sort_order'  => $index + 1,
                    ],
                );
            }
        }
    }
}

