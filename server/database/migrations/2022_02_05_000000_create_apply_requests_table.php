<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateApplyRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_requests', function (Blueprint $table) {
            $table
                ->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->datetime('applied_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('is_accepted')->default(false);
            $table->boolean('is_read')->default(false);

            $table->primary(['user_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apply_requests');
    }
}
