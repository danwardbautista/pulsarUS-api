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
        Schema::create('domain_filter', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('label');
            $table->longText('customData');
            $table->boolean('removed');
            $table->longText('domains');
            $table->longText('filterLists')->nullable();
            $table->integer('version')->default(0);
            $table->integer('firewallVersion')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_filter');
    }
};
