<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licensable_product_id')->constrained()->cascadeOnDelete();
            $table->string('tier_key', 50)->comment('fixed_3day, fixed_7day, fixed_30day, custom');
            $table->integer('duration_days')->nullable()->comment('null for custom tier');
            $table->decimal('discount_pct', 5, 2)->default(0)->comment('0 for custom tier');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['licensable_product_id', 'tier_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_tiers');
    }
};
