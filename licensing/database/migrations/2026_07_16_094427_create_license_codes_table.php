<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licensable_product_id')->constrained()->cascadeOnDelete();
            $table->string('code', 19)->unique()->comment('Format: XXXX-XXXX-XXXX-XXXX');
            $table->integer('duration_days');
            $table->decimal('price_paid', 10, 2);
            $table->string('payment_reference', 255)->nullable()->unique()->comment('Unique index prevents webhook/callback race');
            $table->string('status')->default('unused')->comment('unused, redeemed');
            $table->string('redeemed_by_domain', 255)->nullable();
            $table->string('redeemed_by_client_key', 255)->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'licensable_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_codes');
    }
};
