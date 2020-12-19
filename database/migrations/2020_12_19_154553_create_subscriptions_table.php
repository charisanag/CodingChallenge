<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->decimal("price",4);
        });


        Schema::create('subscriptions', function (Blueprint $table) {
            $table->integer("subscription_type_id")->unsigned();
            $table->string("user_id ")->primary();
            $table->decimal("price",4);
            $table->date("from");
            $table->date("to");
            $table->foreign('subscription_type_id')->references("id")->on("subscription_types");

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_types');

    }
}
