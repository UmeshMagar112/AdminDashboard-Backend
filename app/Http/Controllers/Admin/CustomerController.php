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

    protected array $withAll = ['roles'];
    protected array $withCount = ['orders'];
    protected array $loadAll = ['roles', 'orders'];
    protected bool $applyPermission = true;

    // Only show customers (not admin users)
    protected array $scopes = ['role'];
    protected array $scopeWithValue = ['role' => 'customer'];
}
