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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip_address');
            $table->integer('attempts');
            $table->string('email');
            $table->string('password')->nullable();
            $table->timestamp('login_attempt_date')->nullable();
            $table->timestamps();

        });
    }

    /**j
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_attempts');
    }
};
