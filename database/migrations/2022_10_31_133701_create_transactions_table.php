<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('transaction_id')->nullable();
            $table->json('item')->nullable();
            $table->string('description')->nullable();
            $table->float('transaction_amount')->nullable();
            $table->string('barcode')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->string('payment_type_id')->nullable();
            $table->longText('external_resource_url')->nullable();
            $table->float('total_paid_amount')->nullable();
            $table->string('last_four_digits', 4)->nullable();
            $table->string('status', 4)->nullable();
            $table->string('status_detail', 4)->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
