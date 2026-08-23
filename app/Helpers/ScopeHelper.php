<?php
// app/Helpers/ScopeHelper.php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

/**
 * ScopeHelper - Logika pembatasan akses data berdasarkan hierarki role 4-Tier:
 *
 *   [ Direktur / HD ]  --> Akses semua data global (laporan menyeluruh, approval akhir)
 *          │
 *          ▼
 *   [ Group Leader (GL) ] --> 1 Orang - Menjembatani & mengatur SEMUA TIM & seluruh engineer
 *    ┌─────┴─────┐
 *    ▼           ▼
 * [ TL Tim A ] [ TL Tim B ] --> Hanya mengatur Tim-nya sendiri (Tim A / Tim B)
 *    │           │
 *   Eng         Eng        --> Hanya data & tugas diri sendiri
 */
class ScopeHelper
{
    /**
     * Apakah user memiliki akses ke seluruh tim (Global: Direktur & Group Leader & Lead Engineer lama)?
     */
    public static function isGlobal($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Direktur',
            'HD / Direktur',
            'Group Leader',
            'Lead Divisi',
            'Lead Engineer',
        ]);
    }

    /**
     * Apakah user adalah Group Leader (GL)?
     */
    public static function isGroupLeader($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole(['Group Leader', 'Lead Divisi']);
    }

    /**
     * Apakah user adalah Team Leader (TL)?
     */
    public static function isTeamLeader($user): bool
    {
        if (!$user) return false;
        return $user->hasRole('Team Leader');
    }

    /**
     * Apakah user adalah level manajerial (bisa mengatur/melihat data bawahan)?
     * Mencakup: Direktur, Group Leader, Team Leader, Lead Engineer
     */
    public static function isManagerial($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Direktur',
            'HD / Direktur',
            'Group Leader',
            'Lead Divisi',
            'Team Leader',
            'Lead Engineer',
        ]);
    }

    /**
     * Apakah user memiliki wewenang operasional untuk menambah/mengedit project dan membuat/assign task?
     * Hanya Team Leader (Lead Network, Lead Security, dll) dan Lead Engineer.
     * Direktur & Group Leader difokuskan untuk monitoring/supervisi.
     */
    public static function canManageProjectsAndTasks($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole(['Team Leader', 'Lead Engineer']);
    }

    /**
     * Ambil daftar ID user yang berada dalam scope wewenang user yang login.
     * Digunakan untuk query filter task, schedule, presensi, dll.
     *
     * @return array|null null = akses semua user (Global: Direktur / Group Leader), array = ID user dalam scope
     */
    public static function getScopeUserIds($user): ?array
    {
        if (!$user) return [];

        // 1. Direktur & Group Leader (GL) -> Akses SELURUH tim & seluruh engineer
        if (self::isGlobal($user)) {
            return null;
        }

        // 2. Leader Divisi (Network Leader / Security Leader) -> Akses SEMUA engineer di divisinya
        if (self::isTeamLeader($user)) {
            if ($user->division_id) {
                return \App\Models\User::where('division_id', $user->division_id)
                    ->pluck('id')
                    ->toArray();
            }
            if ($user->team_id) {
                return \App\Models\User::where('team_id', $user->team_id)
                    ->pluck('id')
                    ->toArray();
            }
            return [$user->id];
        }

        // 3. Engineer -> Hanya dirinya sendiri
        return [$user->id];
    }

    /**
     * Terapkan scope query berdasarkan kolom user_id/engineer_id.
     *
     * @param Builder $query       Query Eloquent
     * @param mixed   $user        User yang sedang login
     * @param string  $column      Nama kolom (default: 'user_id')
     * @return Builder
     */
    public static function applyScope(Builder $query, $user, string $column = 'user_id'): Builder
    {
        $ids = self::getScopeUserIds($user);

        if ($ids === null) {
            // Akses global -> tidak ada filter tambahan
            return $query;
        }

        if (count($ids) === 1) {
            return $query->where($column, $ids[0]);
        }

        return $query->whereIn($column, $ids);
    }

    /**
     * Ambil daftar engineer/personel teknis lapangan yang bisa dipilih/di-assign task & jadwal.
     * Mencakup Team Leader (TL) dan seluruh Engineer lapangan.
     * Tidak mencakup Direktur dan Kepala Divisi / Group Leader (Susanto Djaya).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAssignableEngineers($user)
    {
        $operationalRoles = [
            'Team Leader',
            'Lead Engineer',
            'Engineer',
            'Engineer L1',
            'Engineer L2',
        ];

        // 1. Direktur & Group Leader (GL) -> Bisa assign ke SEMUA personel teknis (TL, GL, Engineer) di seluruh divisi
        if (self::isGlobal($user)) {
            return \App\Models\User::role($operationalRoles)->active()->get();
        }

        // 2. Leader Divisi (Network Leader / Security Leader) -> Assign ke personel di divisinya + dirinya sendiri
        if (self::isTeamLeader($user)) {
            if ($user->division_id) {
                return \App\Models\User::role($operationalRoles)
                    ->active()
                    ->where(function($q) use ($user) {
                        $q->where('division_id', $user->division_id)
                          ->orWhere('id', $user->id);
                    })
                    ->get();
            }
            if ($user->team_id) {
                return \App\Models\User::role($operationalRoles)
                    ->active()
                    ->where(function($q) use ($user) {
                        $q->where('team_id', $user->team_id)
                          ->orWhere('id', $user->id);
                    })
                    ->get();
            }
        }

        // 3. Engineer -> Hanya dirinya sendiri
        return collect([$user]);
    }
}
