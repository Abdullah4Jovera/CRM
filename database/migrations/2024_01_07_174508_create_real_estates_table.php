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
        Schema::create('real_estates', function (Blueprint $table) {
            $table->increment('id');
            $table->unsignedBigInteger('lead_id');
            $table->string('locationChoice')->nullable();
            $table->string('propertyPurpose')->nullable();
            $table->string('propertyType')->nullable();
            $table->string('priceRange')->nullable();
            $table->string('propertyTypeSale')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('real_estates');
    }
};
