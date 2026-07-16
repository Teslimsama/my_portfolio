<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licensable_product_id')->constrained()->cascadeOnDelete();
            $table->string('client_name', 255)->comment('e.g. Bolakaz - shopname.ng');
            $table->string('client_key', 64)->unique()->comment('Public identifier, NOT hashed — indexed for O(1) lookup');
            $table->string('api_secret', 255)->comment('Hashed via Hash::make(), plaintext shown once at creation');
            $table->string('domain', 255)->nullable()->comment('For reference only');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('client_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_credentials');
    }
};
