<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Friendly name e.g. "Panel Utama Lt 1"
            $table->string('device_id')->unique(); // Hardware ID / MAC Address
            $table->string('type'); // Device Type e.g. "PZEM-004T", "ESP32", "DHT22"
            $table->string('location')->nullable(); // Linked Location Name
            $table->string('power_source')->nullable(); // Monitored Power Source Name
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
