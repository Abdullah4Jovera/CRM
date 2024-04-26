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
        Schema::create('pipeline_label', function (Blueprint $table) {
            $table->unsignedBigInteger('pipeline_id');
            $table->unsignedBigInteger('label_id');
            $table->foreign('pipeline_id')->references('id')->on('pipelines');
            $table->foreign('label_id')->references('id')->on('labels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pipeline_label');
    }
};
