<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /** @return array<string,mixed> */
    public function build(): array
    {
        $todaySales = (float) DB::table('sales_orders')->whereDate('order_date', now()->toDateString())->sum('total_amount');
        $monthlyRevenue = (float) DB::table('invoices')->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('total_amount');

        $topSellingProducts = DB::table('sales_order_items')
            ->select('products.name', DB::raw('SUM(sales_order_items.quantity) as qty'))
            ->join('products', 'products.id', '=', 'sales_order_items.product_id')
            ->groupBy('products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $warehouseDistribution = DB::table('stock_movements')
            ->select('warehouses.name', DB::raw('SUM(stock_movements.quantity) as qty'))
            ->join('warehouses', 'warehouses.id', '=', 'stock_movements.warehouse_id')
            ->groupBy('warehouses.name')
            ->get();

        $salesTrends = DB::table('sales_orders')
            ->selectRaw('DATE(order_date) as day, SUM(total_amount) as amount')
            ->whereDate('order_date', '>=', now()->subDays(14)->toDateString())
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $inventoryValue = DB::table('inventory_valuations')
            ->selectRaw('DATE(valuation_date) as day, SUM(value) as value')
            ->whereDate('valuation_date', '>=', now()->subDays(14)->toDateString())
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $lowStockItems = DB::table('products')
            ->select('products.id', 'products.name', DB::raw('COALESCE(SUM(stock_movements.quantity), 0) as stock'))
            ->leftJoin('stock_movements', 'stock_movements.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name', 'products.low_stock_threshold')
            ->havingRaw('COALESCE(SUM(stock_movements.quantity), 0) <= products.low_stock_threshold')
            ->limit(10)
            ->get();

        $outOfStockItems = DB::table('products')
            ->select('products.id', 'products.name', DB::raw('COALESCE(SUM(stock_movements.quantity), 0) as stock'))
            ->leftJoin('stock_movements', 'stock_movements.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->havingRaw('COALESCE(SUM(stock_movements.quantity), 0) <= 0')
            ->limit(10)
            ->get();

        $recentSalesOrders = DB::table('sales_orders')
            ->select('order_number', 'status', 'order_date', 'total_amount')
            ->latest('order_date')
            ->limit(10)
            ->get();

        $recentStockMovements = DB::table('stock_movements')
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'stock_movements.warehouse_id')
            ->select('stock_movements.movement_type', 'stock_movements.quantity', 'stock_movements.created_at', 'products.name as product_name', 'warehouses.name as warehouse_name')
            ->latest('stock_movements.created_at')
            ->limit(10)
            ->get();

        $recentGoodsReceipts = DB::table('goods_receipts')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'goods_receipts.purchase_order_id')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'goods_receipts.warehouse_id')
            ->select('goods_receipts.receipt_number', 'goods_receipts.received_at', 'purchase_orders.order_number as purchase_order_number', 'warehouses.name as warehouse_name')
            ->latest('goods_receipts.received_at')
            ->limit(10)
            ->get();

        return [
            'totalProducts' => Product::count(),
            'totalWarehouses' => Warehouse::count(),
            'lowStockItems' => $lowStockItems,
            'outOfStockItems' => $outOfStockItems,
            'todaySales' => $todaySales,
            'monthlyRevenue' => $monthlyRevenue,
            'topSellingProducts' => $topSellingProducts,
            'recentActivities' => ActivityLog::query()->latest('timestamp')->limit(10)->get(),
            'salesTrends' => $salesTrends,
            'warehouseDistribution' => $warehouseDistribution,
            'inventoryValue' => $inventoryValue,
            'recentSalesOrders' => $recentSalesOrders,
            'recentStockMovements' => $recentStockMovements,
            'recentGoodsReceipts' => $recentGoodsReceipts,
        ];
    }
}
