<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('firewall_rules', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('label');
            $table->longText('customData');
            $table->boolean('removed');
            $table->longText('inbound');
            $table->longText('outbound');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firewall_rules');
    }
};
