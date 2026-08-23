<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Divisions
        if (!Schema::hasTable('divisions')) {
            Schema::create('divisions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabel Teams
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
                $table->foreignId('leader_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Menambahkan kolom ke tabel users (nullable agar tidak merusak data lama)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'division_id')) {
                $table->foreignId('division_id')->nullable()->after('status')->constrained('divisions')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->foreignId('team_id')->nullable()->after('division_id')->constrained('teams')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->string('level', 50)->nullable()->after('team_id')->comment('Tingkatan teknis, misal: L1, L2, Senior, Junior');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'team_id')) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            }
            if (Schema::hasColumn('users', 'division_id')) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            }
            if (Schema::hasColumn('users', 'level')) {
                $table->dropColumn('level');
            }
        });

        Schema::dropIfExists('teams');
        Schema::dropIfExists('divisions');
    }
};
