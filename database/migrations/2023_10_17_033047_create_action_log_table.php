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
        Schema::create('action_log', function (Blueprint $table) {
            $table->id();
            $table->string('accountNum');
            $table->longText('data');
            $table->string('groupID')->nullable();
            $table->string('operation')->nullable();
            $table->string('profileID')->nullable();
            $table->string('serviceID')->nullable();
            $table->string('status')->default("OK");
            $table->string('userID')->nullable();
            $table->string('uID')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_log');
    }
};
