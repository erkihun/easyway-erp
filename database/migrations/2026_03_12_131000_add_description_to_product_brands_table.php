<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_brands', function (Blueprint $table): void {
            if (!Schema::hasColumn('product_brands', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_brands', function (Blueprint $table): void {
            if (Schema::hasColumn('product_brands', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
