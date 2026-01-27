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
        Schema::create('invoice_demo_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('invoice_demo_id')->constrained()->onDelete('cascade');
            
            // Service/Item information
            $table->string('service_name');
            $table->text('description');
            $table->integer('quantity')->default(1);
            $table->decimal('rate', 10, 2);
            $table->decimal('amount', 10, 2);
            
            // Order for display
            $table->integer('sort_order')->default(0);
            
          

            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['invoice_demo_id', 'sort_order']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_demo_items');
    }
}; 