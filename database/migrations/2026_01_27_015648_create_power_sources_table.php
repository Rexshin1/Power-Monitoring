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
        Schema::create('power_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "PLN Utama", "Baterai UPS"
            $table->string('type'); // 'grid' (PLN) or 'battery' (DC)
            $table->float('nominal_voltage')->default(220); // 220V for PLN, 12/24V for Battery
            $table->float('capacity')->nullable(); // Ampere Hour (Ah) for Battery
            $table->float('cost_per_kwh')->nullable(); // Price for PLN
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_sources');
    }
};
