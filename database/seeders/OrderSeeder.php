<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
      public function run(): void
    {
        Order::insert([
            [
                'user_id' => 1,
                'status' => 'pending',
                'total' => 1500.00,
                'payment_status' => 'unpaid',
                'payment_method' => null,
                'notes' => 'First sample order',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'status' => 'processing',
                'total' => 3200.50,
                'payment_status' => 'paid',
                'payment_method' => 'esewa',
                'notes' => 'Second sample order',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'shipped',
                'total' => 7800.00,
                'payment_status' => 'paid',
                'payment_method' => 'khalti',
                'notes' => 'Third sample order',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'status' => 'delivered',
                'total' => 5400.75,
                'payment_status' => 'paid',
                'payment_method' => 'cash_on_delivery',
                'notes' => 'Fourth sample order',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'cancelled',
                'total' => 2100.00,
                'payment_status' => 'unpaid',
                'payment_method' => null,
                'notes' => 'Fifth sample order',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}