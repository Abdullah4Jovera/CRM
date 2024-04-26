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
        Schema::create('business_bankings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id');
            $table->string('business_banking_services');
            $table->string('company_name');
            $table->string('yearly_turnover')->nullable();
            $table->string('have_any_pos')->nullable();
            $table->string('monthly_amount')->nullable();
            $table->string('have_auto_finance')->nullable();
            $table->string('monthly_emi')->nullable();
            $table->string('lgcs')->nullable();
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
        Schema::dropIfExists('business_bankings');
    }
};
