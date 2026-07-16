<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licensable_products', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('name', 255);
            $table->string('code_prefix', 4)->unique()->comment('Used in license code generation, e.g. BLKZ');
            $table->text('description')->nullable();
            $table->decimal('daily_rate', 10, 2)->comment('Base price in ₦ per day');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licensable_products');
    }
};
