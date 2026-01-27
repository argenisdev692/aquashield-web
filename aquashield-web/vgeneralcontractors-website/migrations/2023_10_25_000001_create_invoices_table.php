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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->string('bill_to_name');
            $table->text('bill_to_address')->nullable();
            $table->string('bill_to_phone')->nullable();
            $table->string('bill_to_email')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);
            $table->string('pdf_url')->nullable();
            $table->string('claim_number')->nullable();
            $table->string('policy_number')->nullable();
            $table->string('insurance_company')->nullable();
            $table->date('date_of_loss')->nullable();
            $table->dateTime('date_received')->nullable();
            $table->dateTime('date_inspected')->nullable();
            $table->dateTime('date_entered')->nullable();
            $table->string('price_list_code')->nullable();
            $table->string('type_of_loss')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};