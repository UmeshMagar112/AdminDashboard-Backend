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

    // Adjust stock (add / subtract / set) – accepts frontend format: type, quantity, reason
    public function adjust(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'type'     => ['required', 'in:add,subtract,set'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'reason'   => ['nullable', 'string'],
            'note'     => ['nullable', 'string'],
        ]);

        $inventory = Inventory::findOrFail($id);
        $current = (int) $inventory->quantity;
        $qty = (int) $request->quantity;

        if ($request->type === 'add') {
            $newQty = $current + $qty;
            $delta = $qty;
        } elseif ($request->type === 'subtract') {
            $newQty = max(0, $current - $qty);
            $delta = $newQty - $current;
        } else {
            $newQty = $qty;
            $delta = $newQty - $current;
        }

        $inventory->update(['quantity' => $newQty]);

        $note = $request->reason ?: $request->note ?: sprintf('%s: %d', $request->type, $qty);
        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'type'         => 'adjustment',
            'quantity'     => $delta,
            'note'         => $note,
            'created_by'   => auth()->id(),
        ]);

        $inventory->load($this->loadAll);
        return $this->success('Stock adjusted successfully', new InventoryResource($inventory));
    }
}
