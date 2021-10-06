<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Models extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('environments', function(Blueprint $table) {
            $table->increments('id');
            $table->text('values');
            $table->timestamps();
        });

        Schema::create('stories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('environment_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('environment_id')
                ->references('id')
                ->on('environments');
        });

        Schema::create('tasks', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('order')->unsigned()->default(500);
            $table->integer('story_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('story_id')
                ->references('id')
                ->on('stories')
                ->cascadeOnDelete();
        });

        Schema::create('steps', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('script');
            $table->integer('order')->unsigned()->default(500);
            $table->string('runner'); // Local / SSH / Sync SSH
            $table->integer('task_id')->unsigned();
            $table->timestamps();

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->cascadeOnDelete();
        });

        Schema::create('schedules', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('story_id')->unsigned();
            $table->string('definition');
            $table->text('args')->nullable();
            $table->string('notify')->default(\App\Models\Schedule::NOTIFY_NONE);
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('story_id')->unsigned();
            $table->timestamp('finished_at')->nullable();
            $table->integer('aggregate_exit_code')->nullable();
            $table->timestamps();
        });

        Schema::create('outcomes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // Task Name: Step Name
            $table->integer('session_id')->unsigned();
            $table->integer('step_id')->unsigned();
            $table->integer('exit_code');
            $table->text('output');
            $table->timestamps();

            $table->foreign('session_id')
                ->references('id')
                ->on('sessions')
                ->cascadeOnDelete();

            $table->foreign('step_id')
                ->references('id')
                ->on('steps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outcomes');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('steps');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('environments');
        Schema::dropIfExists('schedules');
    }
}
