<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('task_user')) {
            Schema::create('task_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['task_id', 'user_id']);
            });

            // Otomatis salin data penugasan engineer eksisting ke tabel pivot task_user
            $tasks = DB::table('tasks')->whereNotNull('engineer_id')->get();
            $taskUserData = [];
            $now = now();
            foreach ($tasks as $task) {
                $taskUserData[] = [
                    'task_id'    => $task->id,
                    'user_id'    => $task->engineer_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($taskUserData)) {
                DB::table('task_user')->insertOrIgnore($taskUserData);
            }
        }

        if (!Schema::hasTable('schedule_user')) {
            Schema::create('schedule_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['schedule_id', 'user_id']);
            });

            // Otomatis salin data penugasan engineer eksisting ke tabel pivot schedule_user
            $schedules = DB::table('schedules')->whereNotNull('engineer_id')->get();
            $scheduleUserData = [];
            $now = now();
            foreach ($schedules as $schedule) {
                $scheduleUserData[] = [
                    'schedule_id' => $schedule->id,
                    'user_id'     => $schedule->engineer_id,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
            if (!empty($scheduleUserData)) {
                DB::table('schedule_user')->insertOrIgnore($scheduleUserData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_user');
        Schema::dropIfExists('task_user');
    }
};
