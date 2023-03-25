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
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->foreignId('rol_id')->default(2)->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_type_id')->default(1)->nullable()->constrained('user_types');
            $table->unsignedInteger('life')->default(100);
            $table->unsignedInteger('attack')->default(5);
            $table->unsignedInteger('defense')->default(5);
            $table->timestamps();
            $table->softDeletes();
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
