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
        Schema::create('owners', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->date('birth')->nullable();
            $table->tinyInteger('type')->default(0);
            $table->string('cpf', 15)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('cnpj')->nullable();
            $table->string('ie')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_1', 15)->nullable();
            $table->string('phone_2', 15)->nullable();
            $table->string('phone_3', 15)->nullable();
            $table->string('zipcode', 9)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('street')->nullable();
            $table->string('street_number')->nullable();
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
        Schema::dropIfExists('owners');
    }
};
