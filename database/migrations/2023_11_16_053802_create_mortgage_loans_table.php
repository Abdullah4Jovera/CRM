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
        Schema::create('mortgage_loans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id');
            $table->string('type_of_property');
            $table->string('location');
            $table->string('monthly_income');
            $table->string('have_any_other_loan');
            $table->string('loanAmount')->nullable();
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
        Schema::dropIfExists('mortgage_loans');
    }
};
