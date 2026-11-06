<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cari Novan resmi (Team Leader Security Engineering)
        $novanOfficial = User::where('email', 'novan@ipnetsolusindo.com')
            ->orWhere(function($q) {
                $q->where('name', 'Novan')
                  ->where('position', 'like', '%Security%');
            })
            ->first();

        // 2. Cari Novan duplikat (Novan Pudjirachmanto)
        $novanDuplicate = User::withTrashed()
            ->where(function($q) {
                $q->where('email', 'novan.pudjirachmanto@ipnetsolusindo.com')
                  ->orWhere('name', 'like', '%Pudjirachmanto%');
            })
            ->get();

        foreach ($novanDuplicate as $dup) {
            // Jika Novan resmi belum ada, ubah yang ini menjadi Novan resmi
            if (!$novanOfficial) {
                $dup->name = 'Novan';
                $dup->email = 'novan@ipnetsolusindo.com';
                $dup->position = 'Team Leader Security Engineering';
                $dup->save();
                $novanOfficial = $dup;
                continue;
            }

            // Alihkan referensi pekerjaan jika ada
            if (Schema::hasTable('tasks')) {
                DB::table('tasks')->where('engineer_id', $dup->id)->update(['engineer_id' => $novanOfficial->id]);
            }
            if (Schema::hasTable('schedules')) {
                DB::table('schedules')->where('engineer_id', $dup->id)->update(['engineer_id' => $novanOfficial->id]);
            }
            if (Schema::hasTable('managed_service_tickets')) {
                DB::table('managed_service_tickets')->where('assigned_to', $dup->id)->update(['assigned_to' => $novanOfficial->id]);
            }
            if (Schema::hasTable('managed_service_pm_cycles')) {
                DB::table('managed_service_pm_cycles')->where('lead_engineer_id', $dup->id)->update(['lead_engineer_id' => $novanOfficial->id]);
            }

            // Hapus roles & force delete duplikat
            try {
                $dup->roles()->detach();
            } catch (\Throwable $e) {}

            try {
                $dup->forceDelete();
            } catch (\Throwable $e) {
                $dup->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

