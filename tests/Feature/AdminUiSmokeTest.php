<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\GoodsReceipt;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUiSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_admin_pages_render_for_seeded_admin(): void
    {
        $admin = User::query()->where('email', 'admin@erp.local')->firstOrFail();
        $this->actingAs($admin);

        $product = Product::query()->firstOrFail();
        $warehouse = Warehouse::query()->firstOrFail();
        $transfer = Transfer::query()->firstOrFail();
        $purchase = PurchaseOrder::query()->firstOrFail();
        $receipt = GoodsReceipt::query()->firstOrFail();
        $sale = SalesOrder::query()->firstOrFail();
        $invoice = Invoice::query()->firstOrFail();
        $productionOrder = ProductionOrder::query()->first();
        $journalEntry = JournalEntry::query()->first();

        $routes = [
            route('admin.dashboard'),
            route('admin.products.index'),
            route('admin.products.create'),
            route('admin.products.show', $product),
            route('admin.products.edit', $product),
            route('admin.warehouses.index'),
            route('admin.warehouses.create'),
            route('admin.warehouses.show', $warehouse),
            route('admin.warehouses.edit', $warehouse),
            route('admin.inventory.index'),
            route('admin.inventory.movements'),
            route('admin.inventory.low-stock'),
            route('admin.inventory.adjustments'),
            route('admin.transfers.index'),
            route('admin.transfers.create'),
            route('admin.transfers.show', $transfer),
            route('admin.purchases.index'),
            route('admin.purchases.create'),
            route('admin.purchases.show', $purchase),
            route('admin.goods-receipts.index'),
            route('admin.goods-receipts.create'),
            route('admin.goods-receipts.show', $receipt),
            route('admin.sales.index'),
            route('admin.sales.create'),
            route('admin.sales.show', $sale),
            route('admin.invoices.index'),
            route('admin.invoices.create'),
            route('admin.invoices.show', $invoice),
            route('admin.customers.index'),
            route('admin.customers.create'),
            route('admin.suppliers.index'),
            route('admin.suppliers.create'),
            route('admin.reports.index'),
            route('admin.manufacturing.index'),
            route('admin.manufacturing.boms.index'),
            route('admin.manufacturing.boms.create'),
            route('admin.manufacturing.production-orders.index'),
            route('admin.manufacturing.production-orders.create'),
            route('admin.pos.index'),
            route('admin.pos.session'),
            route('admin.pos.checkout.page'),
            route('admin.accounting.index'),
            route('admin.accounting.accounts.index'),
            route('admin.accounting.journal-entries.index'),
            route('admin.users.index'),
            route('admin.settings.index'),
        ];

        if ($productionOrder !== null) {
            $routes[] = route('admin.manufacturing.production-orders.show', $productionOrder);
        }

        if ($journalEntry !== null) {
            $routes[] = route('admin.accounting.journal-entries.show', $journalEntry);
        }

        foreach ($routes as $uri) {
            $this->get($uri)->assertOk();
        }
    }
}
