<?php
// app/Http/Controllers/Admin/ProductController.php
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends CrudBaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Product::class,
            storeRequest: StoreProductRequest::class,
            updateRequest: UpdateProductRequest::class,
            resource: ProductResource::class,
        );
    }

    protected array $withAll = ['category', 'inventory'];
    protected array $withCount = ['reviews', 'orderItems'];
    protected array $loadAll = ['category', 'images', 'variants.attributeValues.attribute', 'inventory', 'reviews'];
    protected bool $applyPermission = true;

    // Override store to handle images, variants, inventory
    public function store(): JsonResponse
    {
        $request = app(StoreProductRequest::class);
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $product = Product::create($data);

            // Images
            if (!empty($data['images'])) {
                foreach ($data['images'] as $img) {
                    $product->images()->create($img);
                }
            }

            // Inventory (for simple products)
            if (empty($data['variants']) && isset($data['inventory'])) {
                $product->inventory()->create([
                    'quantity'            => $data['inventory']['quantity'] ?? 0,
                    'low_stock_threshold' => $data['inventory']['low_stock_threshold'] ?? 5,
                ]);
            }

            // Variants
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $variant = $product->variants()->create([
                        'sku'           => $variantData['sku'],
                        'price'         => $variantData['price'] ?? null,
                        'compare_price' => $variantData['compare_price'] ?? null,
                    ]);

                    // Attach attribute values
                    $variant->attributeValues()->attach($variantData['attribute_value_ids']);

                    // Variant inventory
                    if (isset($variantData['inventory'])) {
                        Inventory::create([
                            'product_id'          => $product->id,
                            'product_variant_id'  => $variant->id,
                            'quantity'            => $variantData['inventory']['quantity'] ?? 0,
                            'low_stock_threshold' => $variantData['inventory']['low_stock_threshold'] ?? 5,
                        ]);
                    }
                }
            }

            DB::commit();

            $product->load($this->loadAll);
            return $this->success('Product created successfully', new ProductResource($product), 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // Override update to handle inventory update
    public function update($id): JsonResponse
    {
        $request = app(UpdateProductRequest::class);
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->update($data);

            // Sync images if provided
            if (isset($data['images'])) {
                $product->images()->delete();
                foreach ($data['images'] as $img) {
                    $product->images()->create($img);
                }
            }

            // Update inventory
            if (isset($data['inventory'])) {
                $product->inventory()->updateOrCreate(
                    ['product_variant_id' => null],
                    [
                        'quantity'            => $data['inventory']['quantity'] ?? $product->inventory?->quantity ?? 0,
                        'low_stock_threshold' => $data['inventory']['low_stock_threshold'] ?? 5,
                    ]
                );
            }

            DB::commit();

            $product->load($this->loadAll);
            return $this->success('Product updated successfully', new ProductResource($product));

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
}
