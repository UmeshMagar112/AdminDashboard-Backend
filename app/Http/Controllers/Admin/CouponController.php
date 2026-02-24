<?php
// app/Http/Controllers/Admin/CouponController.php
namespace App\Http\Controllers\Admin;

use Anil\FastApiCrud\Controller\CrudBaseController;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Http\Resources\Coupon\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;

class CouponController extends CrudBaseController
{
    public function __construct()
    {
        parent::__construct(
            model: Coupon::class,
            storeRequest: StoreCouponRequest::class,
            updateRequest: UpdateCouponRequest::class,
            resource: CouponResource::class,
        );
    }

    protected array $withCount = ['orders'];
    protected bool $applyPermission = true;

    // Validate coupon by code (for frontend checkout)
    public function validate(string $code): JsonResponse
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon || !$coupon->is_valid) {
            return $this->error('Invalid or expired coupon code', 422);
        }

        return $this->success('Coupon is valid', new CouponResource($coupon));
    }
}
