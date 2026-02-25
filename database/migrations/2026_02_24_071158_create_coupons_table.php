<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('coupons', function (Blueprint $table) {
    $table->id();

    $table->string('code')->unique();
    $table->string('name');
    $table->text('description')->nullable();

    $table->enum('type', ['percentage', 'fixed']);
    $table->decimal('value', 10, 2);

    $table->decimal('minimum_order_amount', 10, 2)->default(0);
    $table->decimal('maximum_discount_amount', 10, 2)->nullable();

    $table->integer('usage_limit')->nullable();
    $table->integer('usage_limit_per_user')->nullable();
    $table->integer('used_count')->default(0);

    $table->boolean('status')->default(true);
    $table->boolean('is_single_use')->default(false);

    $table->timestamp('starts_at')->nullable();
    $table->timestamp('expires_at')->nullable();

    $table->softDeletes();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
