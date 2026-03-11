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
    public function index()
    {
        // Summary statistics
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();

        // Monthly sales data (last 12 months)
        $monthlySales = Order::where('status', 'completed')
            ->where('order_date', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(order_date) as year, MONTH(order_date) as month, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Sales by category
        $salesByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->selectRaw('categories.name as category, SUM(order_items.subtotal) as total')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // Top 10 products by sales
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->selectRaw('products.name as product, SUM(order_items.quantity) as total_qty, SUM(order_items.subtotal) as total_sales')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        // Order status distribution
        $orderStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Top 5 customers by spending
        $topCustomers = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.status', 'completed')
            ->selectRaw('customers.name as customer, SUM(orders.total_amount) as total_spent, COUNT(orders.id) as order_count')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with('customer')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Daily sales (last 30 days)
        $dailySales = Order::where('status', 'completed')
            ->where('order_date', '>=', now()->subDays(30))
            ->selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.index', compact(
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
