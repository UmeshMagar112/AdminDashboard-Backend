<?php
// app/Http/Controllers/Admin/OrderController.php
//
// Admin CRUD controller for orders.
// Uses FastApiCrud for the basic REST actions and overrides store/update to:
//  - calculate line totals and order subtotal/total
//  - apply coupon discounts and track coupon usage
//  - reserve inventory quantities
//  - keep a status history timeline for the order.
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends CrudBaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Order::class,
            storeRequest: StoreOrderRequest::class,
            updateRequest: UpdateOrderRequest::class,
            resource: OrderResource::class,
        );
    }

    public  array $withAll = ['user'];
    public  array $withCount = ['items'];
    public  array $loadAll = ['user', 'items', 'statusHistories.creator'];
    public  bool $applyPermission = true;

    public function store(): JsonResponse
    {
        $request = app(StoreOrderRequest::class);
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $variant = isset($item['product_variant_id'])
                    ? ProductVariant::findOrFail($item['product_variant_id'])
                    : null;

                $unitPrice = $variant?->price ?? $product->price;
                $totalPrice = $unitPrice * $item['quantity'];
                $subtotal += $totalPrice;

                $orderItems[] = [
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name'       => $product->name,
                    'variant_name'       => $variant?->name,
                    'sku'                => $variant?->sku ?? $product->sku,
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $unitPrice,
                    'total_price'        => $totalPrice,
                ];
            }

            $discountAmount = 0;
            $shippingAmount = 0; 
            $taxAmount      = 0; 
            $total          = $subtotal - $discountAmount + $shippingAmount + $taxAmount;

            $order = Order::create([
                'user_id'          => $data['user_id'],
                'payment_method'   => $data['payment_method'] ?? null,
                'subtotal'         => $subtotal,
                'discount_amount'  => $discountAmount,
                'shipping_amount'  => $shippingAmount,
                'tax_amount'       => $taxAmount,
                'total'            => $total,
                'shipping_name'    => $data['shipping_name'],
                'shipping_email'   => $data['shipping_email'],
                'shipping_phone'   => $data['shipping_phone'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'shipping_city'    => $data['shipping_city'],
                'shipping_state'   => $data['shipping_state'] ?? null,
                'shipping_zip'     => $data['shipping_zip'] ?? null,
                'shipping_country' => $data['shipping_country'],
                'notes'            => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($orderItems);

            // Update inventory reserved qty
            foreach ($data['items'] as $item) {
                $inventory = Inventory::where('product_id', $item['product_id'])
                    ->where('product_variant_id', $item['product_variant_id'] ?? null)
                    ->first();
                if ($inventory) {
                    $inventory->increment('reserved_quantity', $item['quantity']);
                }
            }


            // Initial status history
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'pending',
                'comment'    => 'Order placed',
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            $order->load($this->loadAll);
            return $this->success('Order created successfully', new OrderResource($order), 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // Override update to log status changes
    public function update($id): JsonResponse
    {
        $request = app(UpdateOrderRequest::class);
        $data = $request->validated();

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        $order->update($data);

        // Log status change
        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => $data['status'],
                'comment'    => $data['status_comment'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Update timestamps for shipped/delivered
            if ($data['status'] === 'shipped') $order->update(['shipped_at' => now()]);
            if ($data['status'] === 'delivered') $order->update(['delivered_at' => now()]);
        }

        $order->load($this->loadAll);
        return $this->success('Order updated successfully', new OrderResource($order));
    }
}
