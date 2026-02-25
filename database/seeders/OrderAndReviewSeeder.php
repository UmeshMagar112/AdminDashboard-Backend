<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderAndReviewSeeder extends Seeder
{
    public function run(): void
    {
$customers = User::role('customer', 'sanctum')->get();        $products  = Product::take(3)->get();
$admin = User::role('admin', 'sanctum')->first();        $coupon    = Coupon::where('code', 'WELCOME10')->first();

        if ($customers->isEmpty() || $products->isEmpty() || !$admin) {
            $this->command->warn('Skipping orders seeding; need customers, products and admin user.');
            return;
        }

        foreach ($customers as $customer) {
            $product = $products->random();
            $qty     = 2;
            $unit    = $product->price;
            $subtotal = $unit * $qty;
            $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;
            $total    = $subtotal - $discount;

            $order = Order::create([
                'user_id'          => $customer->id,
                'coupon_id'        => $coupon?->id,
                'status'           => 'pending',
                'payment_status'   => 'paid',
                'payment_method'   => 'cod',
                'subtotal'         => $subtotal,
                'discount_amount'  => $discount,
                'shipping_amount'  => 0,
                'tax_amount'       => 0,
                'total'            => $total,
                'shipping_name'    => $customer->name,
                'shipping_email'   => $customer->email,
                'shipping_phone'   => $customer->phone,
                'shipping_address' => 'Sample street 123',
                'shipping_city'    => 'Kathmandu',
                'shipping_state'   => 'Bagmati',
                'shipping_zip'     => '44600',
                'shipping_country' => 'Nepal',
                'notes'            => 'Seeded order',
            ]);

            OrderItem::create([
                'order_id'          => $order->id,
                'product_id'        => $product->id,
                'product_variant_id'=> null,
                'product_name'      => $product->name,
                'variant_name'      => null,
                'sku'               => $product->sku,
                'quantity'          => $qty,
                'unit_price'        => $unit,
                'total_price'       => $subtotal,
            ]);

            // Reserve some inventory if exists
            $inventory = Inventory::where('product_id', $product->id)
                ->whereNull('product_variant_id')
                ->first();
            if ($inventory) {
                $inventory->increment('reserved_quantity', $qty);
            }

            if ($coupon) {
                $coupon->usages()->create([
                    'user_id'  => $customer->id,
                    'order_id' => $order->id,
                ]);
                $coupon->increment('used_count');
            }

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'pending',
                'comment'    => 'Order placed (seed data)',
                'created_by' => $admin->id,
            ]);

            // Create a pending review for dashboard + reviews page
            Review::create([
                'product_id'          => $product->id,
                'user_id'             => $customer->id,
                'order_id'            => $order->id,
                'rating'              => 5,
                'title'               => 'Great product',
                'body'                => 'Really liked it (seeded review).',
                'status'              => 0, // pending
                'is_verified_purchase'=> true,
            ]);
        }
    }
}

