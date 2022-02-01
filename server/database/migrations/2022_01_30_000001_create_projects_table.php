<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate();
            $table->string('project_title');
            $table->string('project_image_uri');
            $table
                ->foreignId('area_id')
                ->constrained('areas')
                ->cascadeOnUpdate();
            $table
                ->foreignId('project_category_id')
                ->constrained('project_categories')
                ->cascadeOnUpdate();
            $table->text('purpose');
            $table->text('description');
            $table->text('ideal_candidate')->nullable();
            $table->text('merit')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
