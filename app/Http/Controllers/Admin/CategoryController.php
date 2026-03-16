<?php
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Request;

class CategoryController extends CrudBaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Category::class,
            storeRequest: StoreCategoryRequest::class,
            updateRequest: UpdateCategoryRequest::class,
            resource: CategoryResource::class,
        );
    }

    public array $withAll = ['parent'];
    public array $withCount = ['products'];
    public array $loadAll = ['parent', 'children'];
    public bool $applyPermission = true;

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
        $category = Category::findOrFail($id);
        $category->update(['status' => $category->status ? 0 : 1]);
        $category->load($this->loadAll);
        return $this->success('Status updated', new CategoryResource($category));
    }
}
