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
        Schema::create('personal_loans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id');
            $table->string('company_name');
            $table->string('monthly_salary');
            $table->string('load_amount');
            $table->string('have_any_loan');
            $table->string('taken_loan_amount')->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('personal_loans');
    }
};
