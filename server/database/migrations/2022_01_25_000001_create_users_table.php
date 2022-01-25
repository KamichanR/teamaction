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
            $table->boolean('linked_with_twitter')->default(false);
            $table->boolean('linked_with_google')->default(false);
            $table->string('twitter_uri')->nullable();
            $table->string('facebook_uri')->nullable();
            $table->string('website_uri')->nullable();
            $table
                ->string('usericon_image_uri')
                ->default('https://firebasestorage.googleapis.com/v0/b/connect-335200.appspot.com/o/default_usericon_image.jpg?alt=media&token=685d350d-140e-4a60-8025-391b297edcb7');
            $table
                ->foreignId('area_id')
                ->nullable()
                ->constrained('areas')
                ->cascadeOnUpdate();
            $table->text('introduction', 500)->nullable();
            $table->text('wanna_do', 500)->nullable();
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
