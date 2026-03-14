<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $allowedPeriods = [30, 90, 180, 365, 0];
        $selectedPeriod = (int) $request->query('period', 365);
        if (!in_array($selectedPeriod, $allowedPeriods, true)) {
            $selectedPeriod = 365;
        }

        $statusOptions = ['all', 'pending', 'processing', 'completed', 'cancelled'];
        $selectedStatus = $request->query('status', 'all');
        if (!in_array($selectedStatus, $statusOptions, true)) {
            $selectedStatus = 'all';
        }

        $dateFrom = $selectedPeriod > 0 ? now()->subDays($selectedPeriod) : null;

        $applyDateFilter = function ($query) use ($dateFrom) {
            if ($dateFrom) {
                $query->whereDate('order_date', '>=', $dateFrom->toDateString());
            }

            return $query;
        };

        $applyStatusFilter = function ($query) use ($selectedStatus) {
            if ($selectedStatus !== 'all') {
                $query->where('status', $selectedStatus);
            }

            return $query;
        };

        // Summary statistics
        $totalRevenueQuery = Order::query();
        $applyDateFilter($totalRevenueQuery);
        if ($selectedStatus === 'all') {
            $totalRevenueQuery->where('status', 'completed');
        } else {
            $totalRevenueQuery->where('status', $selectedStatus);
        }
        $totalRevenue = $totalRevenueQuery->sum('total_amount');

        $totalOrdersQuery = Order::query();
        $applyDateFilter($totalOrdersQuery);
        $applyStatusFilter($totalOrdersQuery);
        $totalOrders = $totalOrdersQuery->count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();

        // Monthly sales data
        $monthlySalesQuery = Order::query();
        $applyDateFilter($monthlySalesQuery);
        if ($selectedStatus === 'all') {
            $monthlySalesQuery->where('status', 'completed');
        } else {
            $monthlySalesQuery->where('status', $selectedStatus);
        }
        $monthlySales = $monthlySalesQuery
            ->selectRaw('YEAR(order_date) as year, MONTH(order_date) as month, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Sales by category
        $salesByCategoryQuery = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->when($dateFrom, function ($query) use ($dateFrom) {
                return $query->whereDate('orders.order_date', '>=', $dateFrom->toDateString());
            })
            ->when($selectedStatus === 'all', function ($query) {
                return $query->where('orders.status', 'completed');
            }, function ($query) use ($selectedStatus) {
                return $query->where('orders.status', $selectedStatus);
            });

        $salesByCategory = $salesByCategoryQuery
            ->selectRaw('categories.name as category, SUM(order_items.subtotal) as total')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // Top 10 products by sales
        $topProductsQuery = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->when($dateFrom, function ($query) use ($dateFrom) {
                return $query->whereDate('orders.order_date', '>=', $dateFrom->toDateString());
            })
            ->when($selectedStatus === 'all', function ($query) {
                return $query->where('orders.status', 'completed');
            }, function ($query) use ($selectedStatus) {
                return $query->where('orders.status', $selectedStatus);
            });

        $topProducts = $topProductsQuery
            ->selectRaw('products.name as product, SUM(order_items.quantity) as total_qty, SUM(order_items.subtotal) as total_sales')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        // Order status distribution
        $orderStatusQuery = Order::query();
        $applyDateFilter($orderStatusQuery);
        $applyStatusFilter($orderStatusQuery);
        $orderStatus = $orderStatusQuery
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Top 5 customers by spending
        $topCustomersQuery = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->when($dateFrom, function ($query) use ($dateFrom) {
                return $query->whereDate('orders.order_date', '>=', $dateFrom->toDateString());
            })
            ->when($selectedStatus === 'all', function ($query) {
                return $query->where('orders.status', 'completed');
            }, function ($query) use ($selectedStatus) {
                return $query->where('orders.status', $selectedStatus);
            });

        $topCustomers = $topCustomersQuery
            ->selectRaw('customers.name as customer, SUM(orders.total_amount) as total_spent, COUNT(orders.id) as order_count')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrdersQuery = Order::with('customer');
        $applyDateFilter($recentOrdersQuery);
        $applyStatusFilter($recentOrdersQuery);
        $recentOrders = $recentOrdersQuery
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Daily sales
        $dailySalesQuery = Order::query();
        $applyDateFilter($dailySalesQuery);
        if ($selectedStatus === 'all') {
            $dailySalesQuery->where('status', 'completed');
        } else {
            $dailySalesQuery->where('status', $selectedStatus);
        }
        $dailySales = $dailySalesQuery
            ->selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.index', compact(
            'selectedPeriod',
            'selectedStatus',
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'monthlySales',
            'salesByCategory',
            'topProducts',
            'orderStatus',
            'topCustomers',
            'recentOrders',
            'dailySales'
        ));
    }
}
