<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = DB::table('categories')->value('id');

        if (!$categoryId) {
            $this->command->warn('No categories found. Please seed categories first.');
            return;
        }

        $products = [
            [
                'name'              => 'Wireless Bluetooth Headphones',
                'short_description' => 'Premium sound quality with noise cancellation.',
                'description'       => 'Experience crystal-clear audio with our wireless Bluetooth headphones. Features active noise cancellation, 30-hour battery life, and foldable design for portability.',
                'price'             => 99.99,
                'compare_price'     => 149.99,
                'cost_price'        => 45.00,
                'thumbnail'         => null,
                'is_featured'       => 1,
            ],
            [
                'name'              => 'Mechanical Keyboard',
                'short_description' => 'Tactile and clicky mechanical switches.',
                'description'       => 'A full-size mechanical keyboard with RGB backlight, blue switches, and durable aluminum frame. Perfect for typing and gaming.',
                'price'             => 79.99,
                'compare_price'     => 109.99,
                'cost_price'        => 35.00,
                'thumbnail'         => null,
                'is_featured'       => 0,
            ],
            [
                'name'              => 'USB-C Hub 7-in-1',
                'short_description' => 'Expand your ports with this compact hub.',
                'description'       => 'Connect up to 7 devices simultaneously with HDMI 4K output, 3x USB-A, SD card reader, and 100W PD charging pass-through.',
                'price'             => 39.99,
                'compare_price'     => 59.99,
                'cost_price'        => 15.00,
                'thumbnail'         => null,
                'is_featured'       => 1,
            ],
            [
                'name'              => 'Ergonomic Office Chair',
                'short_description' => 'Sit comfortably all day long.',
                'description'       => 'Adjustable lumbar support, breathable mesh back, and 360° swivel. Designed for long work sessions.',
                'price'             => 299.99,
                'compare_price'     => 399.99,
                'cost_price'        => 140.00,
                'thumbnail'         => null,
                'is_featured'       => 1,
            ],
            [
                'name'              => 'Smartphone Stand',
                'short_description' => 'Adjustable desk stand for phones and tablets.',
                'description'       => 'Foldable and portable aluminum stand compatible with all smartphones and tablets up to 13 inches.',
                'price'             => 14.99,
                'compare_price'     => null,
                'cost_price'        => 5.00,
                'thumbnail'         => null,
                'is_featured'       => 0,
            ],
        ];

        foreach ($products as $product) {
            $name = $product['name'];
            $slug = Str::slug($name);
            $sku  = strtoupper(Str::random(4)) . '-' . rand(1000, 9999);

            DB::table('products')->insert([
                ...$product,
                'category_id' => $categoryId,
                'slug'        => $slug,
                'sku'         => $sku,
                'status'      => 1,
                'manage_stock'=> 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('Products seeded successfully.');
    }
}