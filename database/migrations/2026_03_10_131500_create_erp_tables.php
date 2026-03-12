<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catalog
        Schema::create('product_categories', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('product_brands', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('units_of_measure', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('symbol', 10)->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignUuid('product_brand_id')->nullable()->constrained('product_brands')->nullOnDelete();
            $table->foreignUuid('unit_of_measure_id')->nullable()->constrained('units_of_measure')->nullOnDelete();
            $table->decimal('low_stock_threshold', 16, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->json('attributes')->nullable();
            $table->decimal('price', 16, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Warehouse
        Schema::create('warehouses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('location')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('warehouse_bins', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->timestamps();
            $table->unique(['warehouse_id', 'code']);
        });

        Schema::create('product_stocks', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('cached_quantity', 16, 4)->default(0);
            $table->decimal('reserved_quantity', 16, 4)->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'warehouse_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('movement_type', 32);
            $table->decimal('quantity', 16, 4);
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->string('reason')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['product_id', 'warehouse_id']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('stock_adjustments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('quantity_delta', 16, 4);
            $table->string('reason');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('inventory_snapshots', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->timestamp('snapshot_at');
            $table->timestamps();
        });

        // Customers
        Schema::create('customer_groups', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_group_id')->nullable()->constrained('customer_groups')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('tax_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_addresses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('label');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Suppliers
        Schema::create('suppliers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('tax_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('supplier_addresses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('label');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Sales
        Schema::create('sales_orders', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('status', 32)->default('draft');
            $table->date('order_date');
            $table->decimal('subtotal', 16, 4)->default(0);
            $table->decimal('tax_amount', 16, 4)->default(0);
            $table->decimal('discount_amount', 16, 4)->default(0);
            $table->decimal('total_amount', 16, 4)->default(0);
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->decimal('unit_price', 16, 4);
            $table->decimal('tax_amount', 16, 4)->default(0);
            $table->decimal('discount_amount', 16, 4)->default(0);
            $table->decimal('line_total', 16, 4);
            $table->timestamps();
        });

        Schema::create('sales_returns', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->string('return_number')->unique();
            $table->date('return_date');
            $table->text('reason')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sales_return_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->decimal('unit_price', 16, 4)->default(0);
            $table->decimal('line_total', 16, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->foreignUuid('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->string('status', 32)->default('draft');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 16, 4)->default(0);
            $table->decimal('paid_amount', 16, 4)->default(0);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('payment_number')->unique();
            $table->string('status', 32)->default('pending');
            $table->string('method', 50)->nullable();
            $table->decimal('amount', 16, 4);
            $table->date('payment_date');
            $table->timestamps();
        });

        // Purchase
        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('status', 32)->default('draft');
            $table->date('order_date');
            $table->decimal('total_amount', 16, 4)->default(0);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->decimal('received_quantity', 16, 4)->default(0);
            $table->decimal('unit_cost', 16, 4);
            $table->decimal('line_total', 16, 4);
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('receipt_number')->unique();
            $table->foreignUuid('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignUuid('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->date('received_at');
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Transfers
        Schema::create('transfers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('transfer_number')->unique();
            $table->foreignUuid('source_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignUuid('destination_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->string('status', 32)->default('draft');
            $table->date('transfer_date');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('transfer_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('transfer_id')->constrained('transfers')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->timestamps();
        });

        // Accounting
        Schema::create('accounts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type', 32);
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entry_number')->unique();
            $table->date('entry_date');
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->text('memo')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('journal_lines', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained('accounts')->restrictOnDelete();
            $table->decimal('debit', 16, 4)->default(0);
            $table->decimal('credit', 16, 4)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_rates', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('rate', 8, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->decimal('exchange_rate', 18, 8)->default(1);
            $table->boolean('is_base')->default(false);
            $table->timestamps();
        });

        // Costing
        Schema::create('cost_layers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->decimal('unit_cost', 16, 4);
            $table->timestamp('layer_date');
            $table->timestamps();
        });

        Schema::create('inventory_valuations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->decimal('value', 16, 4);
            $table->timestamp('valuation_date');
            $table->timestamps();
        });

        // Manufacturing
        Schema::create('boms', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('bom_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('bom_id')->constrained('boms')->cascadeOnDelete();
            $table->foreignUuid('component_product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->timestamps();
        });

        Schema::create('production_orders', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('bom_id')->nullable()->constrained('boms')->nullOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->string('status', 32)->default('draft');
            $table->decimal('planned_quantity', 16, 4);
            $table->decimal('produced_quantity', 16, 4)->default(0);
            $table->date('planned_date')->nullable();
            $table->timestamps();
        });

        // POS
        Schema::create('pos_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32)->default('open');
            $table->decimal('opening_amount', 16, 4)->default(0);
            $table->decimal('closing_amount', 16, 4)->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_orders', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('pos_session_id')->constrained('pos_sessions')->cascadeOnDelete();
            $table->foreignUuid('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('status', 32)->default('completed');
            $table->decimal('total_amount', 16, 4)->default(0);
            $table->timestamp('ordered_at');
            $table->timestamps();
        });

        Schema::create('pos_order_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('pos_order_id')->constrained('pos_orders')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->decimal('unit_price', 16, 4);
            $table->decimal('line_total', 16, 4);
            $table->timestamps();
        });

        // Reporting
        Schema::create('report_cache', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->json('payload');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('analytics_metrics', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('metric');
            $table->date('metric_date');
            $table->decimal('value', 20, 4);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['metric', 'metric_date']);
        });

        // Audit
        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('model');
            $table->uuid('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->index(['model', 'model_id']);
        });

        Schema::create('audit_trails', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event');
            $table->string('subject_type');
            $table->uuid('subject_id')->nullable();
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('system_events', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('severity', 20)->default('info');
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_events');
        Schema::dropIfExists('audit_trails');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('analytics_metrics');
        Schema::dropIfExists('report_cache');
        Schema::dropIfExists('pos_order_items');
        Schema::dropIfExists('pos_orders');
        Schema::dropIfExists('pos_sessions');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('bom_items');
        Schema::dropIfExists('boms');
        Schema::dropIfExists('inventory_valuations');
        Schema::dropIfExists('cost_layers');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('transfer_items');
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('supplier_addresses');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_groups');
        Schema::dropIfExists('inventory_snapshots');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('product_stocks');
        Schema::dropIfExists('warehouse_bins');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('units_of_measure');
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_categories');
    }
};
