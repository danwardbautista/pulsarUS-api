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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->integer('serviceID');
            $table->string('accountNum');
            $table->integer('billingCycleDay');
            $table->timestamp('connectionDateTime');
            $table->string('dataStatus');
            $table->string('voiceStatus');
            $table->integer('deviceID')->nullable();
            $table->timestamp('disconnectionDateTime')->nullable();
            $table->string('displayName');
            $table->integer('groupID');
            $table->string('msisdn');
            $table->string('imsi');
            $table->string('sim');
            $table->string('serviceNum');
            $table->string('networkStatus');
            $table->timestamp('networkStatusUpdateTime')->nullable();
            $table->bigInteger('dataStatusUpdateTime');
            $table->integer('packageCode');
            $table->integer('packageOption');
            $table->timestamp('planStartTime')->nullable();
            $table->string('product');
            $table->string('profile')->nullable();
            $table->string('serviceType');
            $table->string('serviceTypeCode');
            $table->string('status');
            $table->string('varName')->nullable();
            $table->string('deviceType');
            $table->string('deviceProfile')->nullable();
            $table->string('bu');
            $table->string('vesselName')->nullable();
            $table->integer('vesselServiceID')->nullable();
            $table->integer('groupServiceID')->nullable();
            $table->integer('mvsServiceID')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
