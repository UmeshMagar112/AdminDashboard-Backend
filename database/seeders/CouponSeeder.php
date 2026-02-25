<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'name'                   => 'Welcome 10% Off',
                'description'            => '10% discount on first order',
                'type'                   => 'percentage',
                'value'                  => 10,
                'minimum_order_amount'   => 0,
                'maximum_discount_amount'=> 200,
                'usage_limit'            => 100,
                'usage_limit_per_user'   => 1,
                'used_count'             => 0,
                'status'                 => 1,
                'is_single_use'          => 0,
                'starts_at'              => now()->subDay(),
                'expires_at'             => now()->addMonth(),
            ],
        );

        Coupon::firstOrCreate(
            ['code' => 'FLAT500'],
            [
                'name'                   => 'Flat 500 Off',
                'description'            => 'Flat 500 off on orders above 3000',
                'type'                   => 'fixed',
                'value'                  => 500,
                'minimum_order_amount'   => 3000,
                'maximum_discount_amount'=> null,
                'usage_limit'            => 50,
                'usage_limit_per_user'   => 2,
                'used_count'             => 0,
                'status'                 => 1,
                'is_single_use'          => 0,
                'starts_at'              => now()->subDay(),
                'expires_at'             => now()->addMonths(2),
            ],
        );
    }
}

