<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_skill', function (Blueprint $table) {
            $table
                ->foreignId('userId')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreignId('skillId')
                ->constrained('skills')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->primary(['userId', 'skillId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_skill');
    }
}
