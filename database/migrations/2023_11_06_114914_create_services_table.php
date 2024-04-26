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
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->length(11);
            $table->string('service')->nullable();
            $table->string('finance_amount')->nullable();
            $table->string('bank_commission')->nullable();
            $table->string('customer_commission')->nullable();
            $table->string('with_vat_commission')->nullable();
            $table->string('without_vat_commission')->nullable();
            $table->string('term')->nullable();
            $table->string('b_type')->nullable();
            $table->string('plot_no')->nullable();
            $table->string('sector')->nullable();
            $table->string('emirate')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('services');
    }
};
