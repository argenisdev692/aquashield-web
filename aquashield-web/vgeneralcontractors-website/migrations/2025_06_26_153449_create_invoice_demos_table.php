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
        Schema::create('invoice_demos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Invoice header information
            $table->string('invoice_number', 50)->unique();
            $table->date('invoice_date');
            
            // Bill to information
            $table->string('bill_to_name');
            $table->text('bill_to_address');
            $table->string('bill_to_phone', 20)->nullable();
            $table->string('bill_to_email', 100)->nullable();
            
            // Financial information
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2);
            
            // Insurance and claim information
            $table->string('claim_number')->nullable();
            $table->string('policy_number')->nullable();
            $table->string('insurance_company')->nullable();
            $table->date('date_of_loss')->nullable();
            $table->datetime('date_received')->nullable();
            $table->datetime('date_inspected')->nullable();
            $table->datetime('date_entered')->nullable();
            
            // Additional fields
            $table->string('price_list_code')->nullable();
            $table->string('type_of_loss')->nullable();
            $table->text('notes')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled','print_pdf'])->default('print_pdf');
            
            // pdf
            $table->string('pdf_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['invoice_date']);
            $table->index(['status']);
            $table->index(['claim_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_demos');
    }
};
