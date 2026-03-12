<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PosSessionStatus;
use App\Enums\StockMovementType;
use App\Models\AnalyticsMetric;
use App\Models\Bom;
use App\Models\Customer;
use App\Models\GoodsReceipt;
use App\Models\InventoryValuation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PosOrder;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Transfer;
use App\Models\Warehouse;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\PosService;
use App\Services\ProductionService;
use App\Services\PurchaseService;
use App\Services\SalesService;
use App\Services\TransferService;
use App\Support\ActivityLogger;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleBusinessDataSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::query()->where('code', 'MAIN')->firstOrFail();
        $secondaryWarehouse = Warehouse::query()->updateOrCreate(
            ['code' => 'OUTLET'],
            ['name' => 'Outlet Warehouse', 'location' => 'Downtown', 'is_default' => false]
        );

        $supplier = Supplier::query()->firstOrFail();
        $customer = Customer::query()->firstOrFail();
        $products = Product::query()->take(2)->get();
        if ($products->count() < 2) {
            return;
        }

        $purchaseService = app(PurchaseService::class);
        $salesService = app(SalesService::class);
        $transferService = app(TransferService::class);
        $invoiceService = app(InvoiceService::class);
        $paymentService = app(PaymentService::class);
        $productionService = app(ProductionService::class);
        $posService = app(PosService::class);

        $purchaseOrder = PurchaseOrder::query()->where('order_number', 'PO-SEED-001')->first();
        if (! $purchaseOrder) {
            $purchaseOrder = $purchaseService->createOrder([
                'order_number' => 'PO-SEED-001',
                'supplier_id' => $supplier->id,
                'order_date' => now()->subDays(5)->toDateString(),
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 120, 'unit_cost' => 10],
                    ['product_id' => $products[1]->id, 'quantity' => 100, 'unit_cost' => 7],
                ],
            ]);
        }

        if (! GoodsReceipt::query()->where('receipt_number', 'GRN-SEED-001')->exists()) {
            $purchaseService->receiveGoods([
                'receipt_number' => 'GRN-SEED-001',
                'purchase_order_id' => $purchaseOrder->id,
                'warehouse_id' => $warehouse->id,
                'received_at' => now()->subDays(4)->toDateString(),
                'items' => $purchaseOrder->items->map(fn ($item): array => [
                    'purchase_order_item_id' => $item->id,
                    'quantity' => (float) $item->quantity,
                ])->values()->all(),
            ]);
        }

        if (! Transfer::query()->where('transfer_number', 'TRF-SEED-001')->exists()) {
            $transferService->process([
                'transfer_number' => 'TRF-SEED-001',
                'source_warehouse_id' => $warehouse->id,
                'destination_warehouse_id' => $secondaryWarehouse->id,
                'transfer_date' => now()->subDays(3)->toDateString(),
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 15],
                ],
            ]);
        }

        $salesOrder = SalesOrder::query()->where('order_number', 'SO-SEED-001')->first();
        if (! $salesOrder) {
            $salesOrder = $salesService->confirmOrder([
                'order_number' => 'SO-SEED-001',
                'customer_id' => $customer->id,
                'order_date' => now()->subDays(2)->toDateString(),
                'items' => [
                    ['product_id' => $products[0]->id, 'warehouse_id' => $warehouse->id, 'quantity' => 12, 'unit_price' => 20],
                    ['product_id' => $products[1]->id, 'warehouse_id' => $warehouse->id, 'quantity' => 10, 'unit_price' => 15],
                ],
            ]);
        }

        $invoice = Invoice::query()->where('invoice_number', 'INV-SEED-001')->first();
        if (! $invoice) {
            $invoice = $invoiceService->generate([
                'invoice_number' => 'INV-SEED-001',
                'sales_order_id' => $salesOrder->id,
                'invoice_date' => now()->subDay()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'total_amount' => (float) $salesOrder->total_amount,
            ]);
        }

        if (! Payment::query()->where('payment_number', 'PAY-SEED-001')->exists()) {
            $paymentService->record([
                'payment_number' => 'PAY-SEED-001',
                'invoice_id' => $invoice->id,
                'payment_date' => now()->toDateString(),
                'amount' => max(1, (float) $invoice->total_amount * 0.5),
                'method' => 'bank_transfer',
            ]);
        }

        $bom = Bom::query()->firstOrCreate(
            ['code' => 'BOM-SEED-001'],
            ['product_id' => $products[0]->id, 'name' => 'Sample BOM']
        );

        if ($bom->items()->count() === 0) {
            $bom->items()->create(['component_product_id' => $products[1]->id, 'quantity' => 0.5]);
        }

        if (! ProductionOrder::query()->where('order_number', 'MO-SEED-001')->exists()) {
            $order = $productionService->createOrder([
                'order_number' => 'MO-SEED-001',
                'bom_id' => $bom->id,
                'product_id' => $products[0]->id,
                'warehouse_id' => $warehouse->id,
                'planned_quantity' => 4,
                'planned_date' => now()->toDateString(),
            ]);
            $productionService->completeOrder($order);
        }

        $session = PosSession::query()->where('status', PosSessionStatus::Open)->first();
        if (! $session) {
            $session = PosSession::create([
                'warehouse_id' => $secondaryWarehouse->id,
                'user_id' => null,
                'status' => PosSessionStatus::Open,
                'opening_amount' => 100,
                'opened_at' => now()->subHours(3),
            ]);
        }

        if (! PosOrder::query()->where('order_number', 'POS-SEED-001')->exists()) {
            $posService->checkout([
                'order_number' => 'POS-SEED-001',
                'pos_session_id' => $session->id,
                'customer_id' => $customer->id,
                'items' => [
                    ['product_id' => $products[0]->id, 'warehouse_id' => $secondaryWarehouse->id, 'quantity' => 2, 'unit_price' => 22],
                ],
            ]);
        }

        foreach ($products as $product) {
            $qty = (float) DB::table('stock_movements')
                ->where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->sum('quantity');

            InventoryValuation::query()->updateOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'valuation_date' => now()->toDateString()],
                ['quantity' => $qty, 'value' => $qty * 12]
            );

            AnalyticsMetric::query()->updateOrCreate(
                ['metric' => 'stock_level', 'metric_date' => now()->toDateString(), 'metadata' => json_encode(['product_id' => $product->id])],
                ['value' => $qty]
            );
        }

        ActivityLogger::log('seed_purchase_receipt', PurchaseOrder::class, $purchaseOrder->id);
        ActivityLogger::log('seed_sales_order', SalesOrder::class, $salesOrder->id);
        ActivityLogger::log('seed_transfer', Transfer::class, Transfer::query()->where('transfer_number', 'TRF-SEED-001')->value('id'));
        ActivityLogger::log('seed_invoice', Invoice::class, $invoice->id);
        ActivityLogger::log('seed_stock_movement', StockMovement::class, null, null, ['movement_type' => StockMovementType::Purchase->value]);
    }
}

