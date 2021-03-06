<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessConcatPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_concat_persons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('name',30);
            $table->string('phone_number',30)->nullable();
            $table->string('extension_number',30)->nullable();
            $table->string('email',50)->nullable();
            $table->boolean('is_left')->default(false);
            $table->boolean('want_receive_mail')->default(true);
            $table->timestamp('create_date',0)->nullable();
            $table->timestamp('update_date',0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_concat_persons');
    }
}
