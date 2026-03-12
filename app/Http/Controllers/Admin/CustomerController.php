<?php
// ─── app/Http/Controllers/Admin/CustomerController.php ───────────────────────
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\User;

class CustomerController extends CrudBaseController
{
    public function __construct()
    {
        parent::__construct(
            model: User::class,
            storeRequest: StoreCustomerRequest::class,
            updateRequest: UpdateCustomerRequest::class,
            resource: CustomerResource::class,
        );
    }

    public  array $withAll = ['roles'];
    public  array $withCount = ['orders'];
    public  array $loadAll = ['roles', 'orders'];
    public  bool $applyPermission = true;

    // Only show customers (not admin users)
public array $scopeWithValue = ['role' => 'customer'];
}
