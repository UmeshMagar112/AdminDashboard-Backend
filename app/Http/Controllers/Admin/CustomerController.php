<?php
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Request;

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

    public array $withAll = ['roles'];
    public array $withCount = ['orders'];
    public array $loadAll = ['roles', 'orders'];
    public bool $applyPermission = true;
    public array $scopeWithValue = ['role' => 'customer'];

    public function index(): AnonymousResourceCollection
    {
        $filters = array_filter([
            'queryFilter' => Request::query('search'),
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

    public function changeStatus($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => $user->status ? 0 : 1]);
        $user->load($this->loadAll);
        return $this->success('Status updated', new CustomerResource($user));
    }
}
