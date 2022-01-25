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
            $table->string('username', 32);
            $table->string('uid')->unique();
            $table->boolean('linkedWithTwitter')->default(false);
            $table->boolean('linkedWithGoogle')->default(false);
            $table->string('twitterUri')->nullable();
            $table->string('facebookUri')->nullable();
            $table->string('websiteUri')->nullable();
            $table
                ->string('usericonImageUri')
                ->default('https://firebasestorage.googleapis.com/v0/b/connect-335200.appspot.com/o/default_usericon_image.jpg?alt=media&token=685d350d-140e-4a60-8025-391b297edcb7');
            $table
                ->foreignId('areaId')
                ->nullable()
                ->constrained('areas')
                ->cascadeOnUpdate();
            $table->text('introduction', 500)->nullable();
            $table->text('wannaDo', 500)->nullable();
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
        Schema::dropIfExists('users');
    }
}
