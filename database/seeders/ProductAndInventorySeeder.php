<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductAndInventorySeeder extends Seeder
{
    public function run(): void
    {
        // Make sure we have some categories first
        $categories = Category::take(3)->get();
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found; run CategorySeeder first.');
            return;
        }

        $productsData = [
            [
                'name'   => 'iPhone 15',
                'price'  => 1200,
                'sku'    => 'IP15-001',
                'status' => 1,
            ],
            [
                'name'   => 'Gaming Laptop',
                'price'  => 1800,
                'sku'    => 'LAP-GAME-001',
                'status' => 1,
            ],
            [
                'name'   => 'Wireless Headphones',
                'price'  => 150,
                'sku'    => 'HEAD-001',
                'status' => 1,
            ],
        ];

        foreach ($productsData as $index => $data) {
            $category = $categories[$index % $categories->count()];

            $product = Product::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id'       => $category->id,
                    'name'              => $data['name'],
                    'short_description' => $data['name'] . ' short description',
                    'description'       => $data['name'] . ' long description',
                    'price'             => $data['price'],
                    'status'            => $data['status'],
                    'is_featured'       => $index === 0 ? 1 : 0,
                    'manage_stock'      => 1,
                ],
            );

            // Simple inventory per product (no variants)
            $qty = match ($index) {
                0 => 50,  // plenty in stock
                1 => 3,   // low stock
                default => 0, // out of stock
            };

            Inventory::updateOrCreate(
                [
                    'product_id'         => $product->id,
                    'product_variant_id' => null,
                ],
                [
                    'quantity'           => $qty,
                    'reserved_quantity'  => 0,
                    'low_stock_threshold'=> 5,
                ],
            );
        }
    }
}

