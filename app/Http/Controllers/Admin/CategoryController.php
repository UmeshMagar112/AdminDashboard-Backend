<?php
// ─── app/Http/Controllers/Admin/CategoryController.php ───────────────────────
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;

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

    public  array $withAll = ['parent'];
    public  array $withCount = ['products'];
    public  array $loadAll = ['parent', 'children'];
    public  bool $applyPermission = true;
}
