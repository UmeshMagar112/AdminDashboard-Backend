<?php
// app/Http/Controllers/Admin/DashboardController.php
//
// Aggregates key business metrics for the admin dashboard screen.
// Frontend calls: GET /api/admin/dashboard/stats
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Return summary KPI stats, a 7‑day revenue chart, recent orders,
     * and top selling products for display in the dashboard UI.
     */
    public function stats(): JsonResponse
    {
        // Revenue
        $totalRevenue    = Order::where('payment_status', 'paid')->sum('total');
        $monthRevenue    = Order::where('payment_status', 'paid')
                                ->whereMonth('created_at', now()->month)
                                ->sum('total');
        $todayRevenue    = Order::where('payment_status', 'paid')
                                ->whereDate('created_at', today())
                                ->sum('total');

        // Orders
        $totalOrders     = Order::count();
        $pendingOrders   = Order::where('status', 'pending')->count();
        $todayOrders     = Order::whereDate('created_at', today())->count();

        // Customers
        $totalCustomers  = User::role('customer')->count();
        $newCustomers    = User::role('customer')->whereMonth('created_at', now()->month)->count();

        // Products
        $totalProducts   = Product::count();
        $lowStockCount   = Inventory::lowStock()->count();
        $outOfStockCount = Inventory::outOfStock()->count();

        // Reviews
        $pendingReviews  = Review::pending()->count();

        // Revenue chart (last 7 days)
        $revenueChart = collect(range(6, 0))->map(fn($day) => [
            'date'    => now()->subDays($day)->format('Y-m-d'),
            'revenue' => (float) Order::where('payment_status', 'paid')
                                       ->whereDate('created_at', now()->subDays($day))
                                       ->sum('total'),
            'orders'  => Order::whereDate('created_at', now()->subDays($day))->count(),
        ]);

        // Recent orders
        $recentOrders = Order::with(['user', 'coupon'])
                             ->withCount('items')
                             ->latest()
                             ->take(10)
                             ->get();

        // Top selling products
        $topProducts = Product::withCount('orderItems')
                               ->with('category')
                               ->orderByDesc('order_items_count')
                               ->take(5)
                               ->get();

        return response()->json([
            'stats' => [
                'revenue' => [
                    'total'   => (float) $totalRevenue,
                    'month'   => (float) $monthRevenue,
                    'today'   => (float) $todayRevenue,
                ],
                'orders' => [
                    'total'   => $totalOrders,
                    'pending' => $pendingOrders,
                    'today'   => $todayOrders,
                ],
                'customers' => [
                    'total' => $totalCustomers,
                    'new'   => $newCustomers,
                ],
                'products' => [
                    'total'        => $totalProducts,
                    'low_stock'    => $lowStockCount,
                    'out_of_stock' => $outOfStockCount,
                ],
                'reviews' => [
                    'pending' => $pendingReviews,
                ],
            ],
            'revenue_chart' => $revenueChart,
            'recent_orders' => OrderResource::collection($recentOrders),
            'top_products'  => $topProducts->map(fn($p) => [
                'id'           => $p->id,
                'name'         => $p->name,
                'thumbnail'    => $p->thumbnail,
                'category'     => $p->category?->name,
                'orders_count' => $p->order_items_count,
                'price'        => (float) $p->price,
            ]),
        ]);
    }
}
