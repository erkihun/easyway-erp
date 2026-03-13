<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            if (! Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency', 10)->default('ETB')->after('due_date');
            }
            if (! Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('paid_amount');
            }
        });

        Schema::table('payments', function (Blueprint $table): void {
            if (! Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable()->after('method');
            }
            if (! Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable()->after('amount');
            }
        });

        if (! Schema::hasTable('credit_notes')) {
            Schema::create('credit_notes', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('credit_note_number')->unique();
                $table->foreignUuid('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->date('credit_date');
                $table->decimal('amount', 16, 4);
                $table->text('reason')->nullable();
                $table->string('status', 32)->default('issued');
                $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('refunds')) {
            Schema::create('refunds', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('refund_number')->unique();
                $table->foreignUuid('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->foreignUuid('credit_note_id')->nullable()->constrained('credit_notes')->nullOnDelete();
                $table->date('refund_date');
                $table->decimal('amount', 16, 4);
                $table->string('method', 50)->nullable();
                $table->text('reason')->nullable();
                $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('credit_notes');

        Schema::table('payments', function (Blueprint $table): void {
            if (Schema::hasColumn('payments', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('payments', 'notes')) {
                $table->dropColumn('notes');
            }
        });

        Schema::table('invoices', function (Blueprint $table): void {
            if (Schema::hasColumn('invoices', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('invoices', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};

