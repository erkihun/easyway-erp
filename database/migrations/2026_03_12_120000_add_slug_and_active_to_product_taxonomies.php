<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('description');
        });

        Schema::table('product_brands', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('code');
        });

        DB::table('product_categories')->orderBy('created_at')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                $seed = trim((string) ($row->name ?? $row->code ?? 'category'));
                $baseSlug = Str::slug($seed);
                if ($baseSlug === '') {
                    $baseSlug = 'category-' . Str::lower((string) Str::uuid());
                }

                $slug = $baseSlug;
                $counter = 1;
                while (DB::table('product_categories')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                DB::table('product_categories')->where('id', $row->id)->update(['slug' => $slug]);
            }
        }, 'id');

        DB::table('product_brands')->orderBy('created_at')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                $seed = trim((string) ($row->name ?? $row->code ?? 'brand'));
                $baseSlug = Str::slug($seed);
                if ($baseSlug === '') {
                    $baseSlug = 'brand-' . Str::lower((string) Str::uuid());
                }

                $slug = $baseSlug;
                $counter = 1;
                while (DB::table('product_brands')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                DB::table('product_brands')->where('id', $row->id)->update(['slug' => $slug]);
            }
        }, 'id');

        Schema::table('product_categories', function (Blueprint $table): void {
            $table->unique('slug');
        });

        Schema::table('product_brands', function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'is_active']);
        });

        Schema::table('product_brands', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'is_active']);
        });
    }
};
