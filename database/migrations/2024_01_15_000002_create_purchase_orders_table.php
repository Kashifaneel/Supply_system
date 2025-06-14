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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('po_number')->unique();
            $table->date('po_date');
            $table->string('po_image')->nullable();
            $table->string('institution_name');
            $table->string('institution_email')->nullable();
            $table->string('institution_phone')->nullable();
            $table->text('institution_address')->nullable();
            $table->enum('status', ['Pending', 'Partially Supplied', 'Fully Supplied'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
