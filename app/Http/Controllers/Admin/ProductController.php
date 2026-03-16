<?php
// app/Http/Controllers/Admin/ProductController.php
//
// Admin CRUD controller for products.
// Extends FastApiCrud's CrudBaseController to get index/show/update/destroy
// for free, and overrides store/update when we need custom logic for:
//  - product images
//  - variants and their attribute values
//  - per‑product / per‑variant inventory records
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

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

    public array $withAll = ['category', 'inventory'];
    public array $withCount = [ 'orderItems'];
    public array $loadAll = ['category', 'images', 'variants.attributeValues.attribute', 'inventory'];
    public bool $applyPermission = true;

    /** Build filters from query params (search, category_id, trashed) for FastApiCrud initializer */
    public function index(): AnonymousResourceCollection
    {
        $filters = array_filter([
            'queryFilter' => Request::query('search'),
            'categoryIds' => Request::query('category_id') ? [Request::query('category_id')] : null,
            'trashed'     => Request::query('trashed'),
        ]);
        if ($filters !== []) {
            request()->query->add(['filters' => json_encode($filters)]);
        }
        if (Request::filled('per_page') && !Request::filled('rowsPerPage')) {
            request()->query->add(['rowsPerPage' => Request::query('per_page')]);
        }
        return parent::index();
    }

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
            $fillable = array_flip((new Product())->getFillable());
            $productData = array_intersect_key($data, $fillable);
            $product->update($productData);

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

    /** Permanently delete product (force delete). */
    public function destroy($id): JsonResponse
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();
        return $this->success('Product deleted permanently');
    }

    /** Override: Product casts status to boolean, so strict === 1 fails. Toggle by truthy check. */
    public function changeStatus($id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => $product->status ? 0 : 1]);
        $product->load($this->loadAll);
        return $this->success('Status updated', new ProductResource($product));
    }

    /** Toggle is_featured (single argument, no route param for column). */
    public function changeFeatured($id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['is_featured' => $product->is_featured ? 0 : 1]);
        $product->load($this->loadAll);
        return $this->success('Featured updated', new ProductResource($product));
    }
}
