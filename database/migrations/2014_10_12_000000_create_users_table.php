<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            /** Foreign Keys area  */
            // $table->foreignId('address_id')->constrained('addresses')->onUpdate('cascade');
            $table->unsignedBigInteger('address_id')->nullable();
            /** End Foreign */
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username',1000)->unique();
            $table->string('password');
            $table->string('email',1000)->unique();
            $table->string('phone_number',14);

            $table->enum('gender',['m','f'])->nullable();
            $table->enum(
                'role',
                ['admin', 'pharmacy_owner', 'user', 'supervisor', 'pharmacist']
                )->default('user');
            $table->enum('status',['activate','non-activate','banned'])->default('non-activate');

            $table->timestamp('last_seen')->useCurrent();
            $table->timestamp('create_time')->useCurrent();
            //$table->rememberToken();
            //$table->timestamps();
        });
        /**Foreign Keys Constraints */
        Schema::table('users', function (Blueprint $table){
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
