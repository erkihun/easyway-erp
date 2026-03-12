<?php
declare(strict_types=1);

use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoodsReceiptController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ManufacturingController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportPageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalesOrderController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::redirect('/', '/admin/dashboard');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('products', ProductController::class)->middleware('permission:create_products|update_products|delete_products');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import')->middleware('permission:create_products');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export')->middleware('permission:view_reports');
    Route::get('products/{product}/barcode', [ProductController::class, 'barcode'])->name('products.barcode')->middleware('permission:create_products|update_products');

    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index')->middleware('permission:manage_stock');
    Route::get('inventory/ledger', [InventoryController::class, 'ledger'])->name('inventory.ledger')->middleware('permission:manage_stock');
    Route::get('inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements')->middleware('permission:manage_stock');
    Route::get('inventory/warehouses', [InventoryController::class, 'warehouses'])->name('inventory.warehouses')->middleware('permission:manage_stock');
    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock')->middleware('permission:manage_stock');
    Route::get('inventory/adjustments', [InventoryController::class, 'adjustments'])->name('inventory.adjustments')->middleware('permission:manage_stock');
    Route::get('inventory/valuation', [InventoryController::class, 'valuation'])->name('inventory.valuation')->middleware('permission:manage_stock');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust')->middleware('permission:manage_stock');

    Route::resource('warehouses', WarehouseController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update'])->middleware('permission:manage_stock');

    Route::resource('transfers', TransferController::class)->only(['index', 'create', 'store', 'show'])->middleware('permission:manage_transfers');

    Route::resource('purchases', PurchaseOrderController::class)->only(['index', 'create', 'store', 'show'])->middleware('permission:manage_purchases');
    Route::resource('goods-receipts', GoodsReceiptController::class)->only(['index', 'create', 'store', 'show'])->middleware('permission:manage_purchases');

    Route::resource('sales', SalesOrderController::class)->only(['index', 'create', 'store', 'show'])->middleware('permission:create_orders');

    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show'])->middleware('permission:manage_accounting|create_orders');
    Route::post('invoices/payments', [InvoiceController::class, 'recordPayment'])->name('invoices.payments')->middleware('permission:manage_accounting');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf')->middleware('permission:create_orders|manage_accounting');

    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:create_orders|manage_users');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('permission:create_orders|manage_users');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:create_orders|manage_users');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:create_orders|manage_users');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:create_orders|manage_users');

    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index')->middleware('permission:manage_purchases|manage_users');
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('permission:manage_purchases|manage_users');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:manage_purchases|manage_users');
    Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('permission:manage_purchases|manage_users');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:manage_purchases|manage_users');

    Route::get('manufacturing', [ManufacturingController::class, 'index'])->name('manufacturing.index')->middleware('permission:manage_stock');
    Route::get('manufacturing/boms', [ManufacturingController::class, 'bomsIndex'])->name('manufacturing.boms.index')->middleware('permission:manage_stock');
    Route::get('manufacturing/boms/create', [ManufacturingController::class, 'bomsCreate'])->name('manufacturing.boms.create')->middleware('permission:manage_stock');
    Route::post('manufacturing/boms', [ManufacturingController::class, 'bomsStore'])->name('manufacturing.boms.store')->middleware('permission:manage_stock');
    Route::get('manufacturing/production-orders', [ManufacturingController::class, 'productionOrdersIndex'])->name('manufacturing.production-orders.index')->middleware('permission:manage_stock');
    Route::get('manufacturing/production-orders/create', [ManufacturingController::class, 'productionOrdersCreate'])->name('manufacturing.production-orders.create')->middleware('permission:manage_stock');
    Route::post('manufacturing/production-orders', [ManufacturingController::class, 'productionOrdersStore'])->name('manufacturing.production-orders.store')->middleware('permission:manage_stock');
    Route::get('manufacturing/production-orders/{productionOrder}', [ManufacturingController::class, 'productionOrdersShow'])->name('manufacturing.production-orders.show')->middleware('permission:manage_stock');
    Route::post('manufacturing/{productionOrder}/complete', [ManufacturingController::class, 'complete'])->name('manufacturing.complete')->middleware('permission:manage_stock');

    Route::get('pos', [PosController::class, 'index'])->name('pos.index')->middleware('permission:operate_pos');
    Route::get('pos/session', [PosController::class, 'sessionPage'])->name('pos.session')->middleware('permission:operate_pos');
    Route::get('pos/checkout', [PosController::class, 'checkoutPage'])->name('pos.checkout.page')->middleware('permission:operate_pos');
    Route::post('pos/sessions', [PosController::class, 'openSession'])->name('pos.sessions.open')->middleware('permission:operate_pos');
    Route::post('pos/sessions/{session}/close', [PosController::class, 'closeSession'])->name('pos.sessions.close')->middleware('permission:operate_pos');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout')->middleware('permission:operate_pos');

    Route::get('accounting', [AccountingController::class, 'index'])->name('accounting.index')->middleware('permission:manage_accounting');
    Route::get('accounting/accounts', [AccountingController::class, 'accountsIndex'])->name('accounting.accounts.index')->middleware('permission:manage_accounting');
    Route::get('accounting/journal-entries', [AccountingController::class, 'journalEntriesIndex'])->name('accounting.journal-entries.index')->middleware('permission:manage_accounting');
    Route::get('accounting/journal-entries/{journalEntry}', [AccountingController::class, 'journalEntryShow'])->name('accounting.journal-entries.show')->middleware('permission:manage_accounting');
    Route::post('accounting/journals', [AccountingController::class, 'postManualEntry'])->name('accounting.journals.store')->middleware('permission:manage_accounting');

    Route::get('reports', [ReportPageController::class, 'index'])->name('reports.index')->middleware('permission:view_reports');
    Route::get('reports/inventory/{format?}', [ReportController::class, 'inventory'])->name('reports.inventory')->middleware('permission:view_reports');
    Route::get('reports/low-stock/{format?}', [ReportController::class, 'lowStock'])->name('reports.low-stock')->middleware('permission:view_reports');
    Route::get('reports/sales/{format?}', [ReportController::class, 'sales'])->name('reports.sales')->middleware('permission:view_reports');
    Route::get('reports/purchase/{format?}', [ReportController::class, 'purchase'])->name('reports.purchase')->middleware('permission:view_reports');
    Route::get('reports/profit/{format?}', [ReportController::class, 'profit'])->name('reports.profit')->middleware('permission:view_reports');
    Route::get('reports/valuation/{format?}', [ReportController::class, 'valuation'])->name('reports.valuation')->middleware('permission:view_reports');

    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view_users|manage_users');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create_users|manage_users');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create_users|manage_users');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:view_users|manage_users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:update_users|manage_users');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:update_users|manage_users');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete_users|manage_users');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:view_roles|manage_users');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:create_roles|manage_users');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:create_roles|manage_users');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:view_roles|manage_users');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:update_roles|manage_users');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:update_roles|manage_users');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete_roles|manage_users');

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:view_permissions|manage_users');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('permission:manage_settings');
    Route::post('settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general')->middleware('permission:manage_settings');
    Route::post('settings/branding', [SettingsController::class, 'updateBranding'])->name('settings.branding')->middleware('permission:manage_settings');
});

