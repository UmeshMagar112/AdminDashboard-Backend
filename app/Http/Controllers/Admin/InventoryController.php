<?php
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Resources\Inventory\InventoryResource;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends CrudBaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Inventory::class,
            storeRequest: StoreInventoryRequest::class,
            updateRequest: UpdateInventoryRequest::class,
            resource: InventoryResource::class,
        );
    }

    public  array $withAll = ['product', 'variant'];
    public  array $loadAll = ['product', 'variant', 'transactions.creator'];
    public  bool $applyPermission = true;

    // Adjust stock (add/subtract)
    public function adjust(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'type'     => ['required', 'in:purchase,adjustment,damage,return'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'note'     => ['nullable', 'string'],
        ]);

        $inventory = Inventory::findOrFail($id);

        $newQty = $inventory->quantity + $request->quantity;
        if ($newQty < 0) {
            return $this->error('Adjustment would result in negative stock', 422);
        }

        $inventory->update(['quantity' => $newQty]);

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'type'         => $request->type,
            'quantity'     => $request->quantity,
            'note'         => $request->note,
            'created_by'   => auth()->id(),
        ]);

        $inventory->load($this->loadAll);
        return $this->success('Stock adjusted successfully', new InventoryResource($inventory));
    }
}
