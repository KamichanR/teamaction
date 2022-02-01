<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_roles', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnUpdate();
            $table->string('role_title');
            $table->unsignedInteger('number_of_applicants');
            $table->date('close');
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_roles');
    }
}
