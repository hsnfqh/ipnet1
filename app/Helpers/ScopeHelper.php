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
     * Apakah user adalah Team Leader (TL) / Lead Divisi / Lead Maintenance?
     */
    public static function isTeamLeader($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole(['Team Leader', 'Lead Maintenance']);
    }

    /**
     * Apakah user adalah level manajerial (bisa mengatur/melihat data bawahan)?
     * Mencakup: Direktur, Group Leader, Team Leader, Lead Maintenance, Lead Engineer
     */
    public static function isManagerial($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Direktur',
            'HD / Direktur',
            'Group Leader',
            'PMO',
            'Project Manager',
            'Lead Divisi',
            'Team Leader',
            'Lead Maintenance',
            'Lead Engineer',
        ]);
    }

    /**
     * Apakah user adalah PMO atau Project Manager?
     */
    public static function isPmo($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole(['PMO', 'Project Manager']);
    }

    /**
     * Apakah user memiliki wewenang operasional untuk membuat project baru?
     * Hanya Team Leader teknis (Network Leader, Security Leader), PMO, Project Manager, dan Lead Engineer.
     * Lead Maintenance bertindak sebagai Helpdesk / Dispatcher sehingga tidak membuat project baru dari nol.
     */
    public static function canCreateProjects($user): bool
    {
        if (!$user) return false;
        if ($user->hasRole('Lead Maintenance')) return false;
        return $user->hasAnyRole(['Direktur', 'HD / Direktur', 'Group Leader', 'PMO', 'Project Manager', 'Lead Divisi', 'Team Leader', 'Lead Engineer']);
    }

    /**
     * Apakah user memiliki wewenang operasional untuk mengelola task/tiket (buat & edit task)?
     * Mencakup Team Leader teknis, Lead Maintenance (Helpdesk), PMO, Project Manager, dan Lead Engineer.
     */
    public static function canManageTasks($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole(['Direktur', 'Group Leader', 'PMO', 'Project Manager', 'Team Leader', 'Lead Maintenance', 'Lead Engineer']);
    }

    /**
     * Apakah user memiliki wewenang operasional (proyek/task)?
     * Deprecated: Gunakan canCreateProjects atau canManageTasks untuk pengecekan spesifik.
     */
    public static function canManageProjectsAndTasks($user): bool
    {
        return self::canManageTasks($user);
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

        // 2. Lead Maintenance -> Sebagai Helpdesk/Koordinator, dapat memantau seluruh teknisi & tiket yang didelegasikan
        if ($user->hasRole('Lead Maintenance')) {
            return null;
        }

        // 3. Leader Divisi (Network Leader / Security Leader) -> Akses SEMUA engineer di divisinya
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

        // 4. Engineer / Maintenance Staff -> Hanya dirinya sendiri
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
     * Mencakup Team Leader (TL), Lead Maintenance, seluruh Engineer & Maintenance lapangan.
     * Tidak mencakup Direktur dan Kepala Divisi / Group Leader (Susanto Djaya).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAssignableEngineers($user)
    {
        $operationalRoles = [
            'Team Leader',
            'Lead Maintenance',
            'Lead Engineer',
            'Engineer',
            'Maintenance',
            'Engineer L1',
            'Engineer L2',
        ];

        // 1. Direktur, Group Leader (GL), DAN Lead Maintenance (Helpdesk Dispatcher)
        // -> Dapat menugaskan (dispatch) ke SEMUA personel teknis (Network & Security Engineer)
        if (self::isGlobal($user) || $user->hasRole('Lead Maintenance')) {
            return \App\Models\User::whereHas('roles', function($q) use ($operationalRoles) {
                $q->whereIn('name', $operationalRoles);
            })->active()->get();
        }

        // 2. Leader Divisi Teknis (Network Leader / Security Leader) -> Assign ke personel di divisinya + dirinya sendiri
        if (self::isTeamLeader($user)) {
            if ($user->division_id) {
                return \App\Models\User::whereHas('roles', function($q) use ($operationalRoles) {
                    $q->whereIn('name', $operationalRoles);
                })
                    ->active()
                    ->where(function($q) use ($user) {
                        $q->where('division_id', $user->division_id)
                          ->orWhere('id', $user->id);
                    })
                    ->get();
            }
            if ($user->team_id) {
                return \App\Models\User::whereHas('roles', function($q) use ($operationalRoles) {
                    $q->whereIn('name', $operationalRoles);
                })
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

    /**
     * Role apa saja yang dapat dibuat oleh user yang sedang login?
     */
    public static function getCreatableRoles($user): array
    {
        if (!$user) return [];

        if ($user->hasAnyRole(['Direktur', 'HD / Direktur'])) {
            return ['Direktur', 'Group Leader', 'PMO', 'Project Manager', 'Team Leader', 'Lead Maintenance', 'Engineer', 'Maintenance'];
        }

        if (self::isGroupLeader($user)) {
            return ['PMO', 'Project Manager', 'Team Leader', 'Lead Maintenance', 'Engineer', 'Maintenance'];
        }

        if (self::isTeamLeader($user)) {
            // Team Leader hanya dapat merekrut/menambah Engineer di divisinya
            return ['Engineer'];
        }

        if ($user->hasRole('Lead Maintenance')) {
            return ['Maintenance'];
        }

        return [];
    }

    /**
     * Role apa saja yang relevan ditampilkan di filter pencarian user?
     */
    public static function getFilterableRoles($user): array
    {
        if (!$user) return [];

        if ($user->hasAnyRole(['Direktur', 'HD / Direktur'])) {
            return ['Direktur', 'Group Leader', 'PMO', 'Project Manager', 'Team Leader', 'Engineer L1', 'Engineer L2'];
        }

        if (self::isGroupLeader($user)) {
            return ['PMO', 'Project Manager', 'Team Leader', 'Engineer L1', 'Engineer L2'];
        }

        if (self::isTeamLeader($user)) {
            // Untuk Team Leader: Tim teknis terdiri dari Engineer L1, Engineer L2, dan dirinya sendiri
            return ['Engineer L1', 'Engineer L2', 'Team Leader'];
        }

        return ['Engineer L1', 'Engineer L2'];
    }

    /**
     * Apakah auth user berhak mengelola (edit, toggle status, hapus) target user?
     */
    public static function canManageUser($authUser, $targetUser): bool
    {
        if (!$authUser || !$targetUser) return false;

        // Tidak dapat mengubah status atau menghapus akun sendiri
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        // 1. Direktur memiliki wewenang penuh atas semua bawahan
        if ($authUser->hasAnyRole(['Direktur', 'HD / Direktur'])) {
            return true;
        }

        // Target adalah Direktur -> tidak ada yang boleh mengubah selain sesama Direktur
        if ($targetUser->hasAnyRole(['Direktur', 'HD / Direktur'])) {
            return false;
        }

        // 2. Group Leader dapat mengelola Team Leader, Lead Maintenance, Engineer, Maintenance
        if (self::isGroupLeader($authUser)) {
            // Tidak dapat mengelola sesama Group Leader
            if ($targetUser->hasAnyRole(['Group Leader', 'Lead Divisi'])) {
                return false;
            }
            return true;
        }

        // 3. Team Leader hanya dapat mengelola Engineer di divisinya sendiri
        if (self::isTeamLeader($authUser)) {
            if ($targetUser->hasAnyRole(['Direktur', 'HD / Direktur', 'Group Leader', 'Lead Divisi', 'Team Leader', 'Lead Maintenance', 'Lead Engineer'])) {
                return false;
            }
            // Harus satu divisi / tim
            if ($authUser->division_id && $targetUser->division_id === $authUser->division_id) {
                return true;
            }
            if ($authUser->team_id && $targetUser->team_id === $authUser->team_id) {
                return true;
            }
            return false;
        }

        // 4. Lead Maintenance hanya dapat mengelola staf Maintenance
        if ($authUser->hasRole('Lead Maintenance')) {
            return $targetUser->hasRole('Maintenance');
        }

        return false;
    }
}
