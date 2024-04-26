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
        Schema::create('service_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('deal_id')->length(11);
            $table->string('finance_amount')->nullable();
            $table->string('bank_commission')->nullable();
            $table->string('customer_commission')->nullable();
            $table->string('with_vat_commission')->nullable();
            $table->string('without_vat_commission')->nullable();
            $table->string("hodsale")->nullable();
            $table->string("hodsalecommission")->nullable();
            $table->string("salemanager")->nullable();
            $table->string("salemanagercommission")->nullable();
            $table->string("coordinator")->nullable();
            $table->string("coordinator_commission")->nullable();
            $table->string("team_leader")->nullable();
            $table->string("team_leader_commission")->nullable();
            $table->string("salesagent")->nullable();
            $table->string("salesagent_commission")->nullable();
            $table->string("salemanagerref")->nullable();
            $table->string("salemanagerrefcommission")->nullable();
            $table->string("agentref")->nullable();
            $table->string("agent_commission")->nullable();
            $table->string("ts_team_leader")->nullable();
            $table->string("ts_team_leader_commission")->nullable();
            $table->string("tsagent")->nullable();
            $table->string("tsagent_commission")->nullable();
            $table->string("marketingmanager")->nullable();
            $table->string("marketingmanagercommission")->nullable();
            $table->string("marketingagent")->nullable();
            $table->string("marketingagentcommission")->nullable();
            $table->string('other_name')->nullable();
            $table->string('other_name_commission')->nullable();
            $table->string('broker_name')->nullable();
            $table->string('broker_name_commission')->nullable();
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
        Schema::dropIfExists('service_commissions');
    }
};
