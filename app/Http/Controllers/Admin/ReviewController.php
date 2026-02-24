<?php
// app/Http/Controllers/Admin/ReviewController.php
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Review;

class ReviewController extends CrudBaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Review::class,
            storeRequest: StoreReviewRequest::class,
            updateRequest: UpdateReviewRequest::class,
            resource: ReviewResource::class,
        );
    }

    protected array $withAll = ['user', 'product'];
    protected array $loadAll = ['user', 'product', 'order'];
    protected bool $applyPermission = true;
}
