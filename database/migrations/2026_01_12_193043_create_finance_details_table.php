<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_snapshot_id')
                ->constrained('finance_snapshots')
                ->cascadeOnDelete();
            $table->foreignId('stock_id')
                ->nullable()
                ->constrained('stocks')
                ->nullOnDelete();
            $table->string('source', 120);
            $table->decimal('amount', 14, 2);
            $table->string('currency_code', 3);
            $table->boolean('is_active')->default(true);
            $table->string('comment', 255)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_details');
    }
};
