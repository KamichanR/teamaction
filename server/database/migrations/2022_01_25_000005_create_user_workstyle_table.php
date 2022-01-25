<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWorkstyleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_workstyle', function (Blueprint $table) {
            $table
                ->foreignId('userId')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreignId('workstyleId')
                ->constrained('workstyles')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->primary(['userId', 'workstyleId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_workstyle');
    }
}
