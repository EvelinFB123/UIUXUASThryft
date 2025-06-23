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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id(); // Primary key (required)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Missing in your current migration
            $table->decimal('amount', 12, 2); // Missing
            $table->string('category'); // Missing
            $table->string('description'); // Missing
            $table->string('payment_method')->nullable(); // Missing
            $table->timestamps(); // Created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
