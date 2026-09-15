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
     * Apakah user memiliki hak akses Eksekutif Tertinggi (Director & Division Head)?
     */
    public static function isExecutive($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Director',
            'Direktur',
            'HD / Direktur',
            'Division Head',
        ]);
    }

    /**
     * Apakah user memiliki akses ke seluruh tim (Global: Director, Division Head, Group Leader, PMO, Project Manager)?
     */
    public static function isGlobal($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Director',
            'Direktur',
            'HD / Direktur',
            'Division Head',
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',
            'Lead Divisi',
            'Lead Engineer',
            'PMO',
            'Project Manager',
        ]);
    }

    /**
     * Apakah user adalah Group Leader (GL) atau Division Head?
     */
    public static function isGroupLeader($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',
            'Division Head',
            'Lead Divisi',
        ]);
    }

    /**
     * Apakah user adalah Team Leader (TL) / Lead Divisi / Managed Service Coordinator?
     */
    public static function isTeamLeader($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Team Leader Engineering',
            'Team Leader',
            'Lead Divisi',
            'Lead Engineer',
            'Lead Maintenance',
            'Managed Service',
        ]);
    }

    /**
     * Apakah user adalah level manajerial (bisa mengatur/melihat data bawahan)?
     * Mencakup: Director, Division Head, Group Leader, PMO, Project Manager, Team Leader, Managed Service
     */
    public static function isManagerial($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Director',
            'Direktur',
            'HD / Direktur',
            'Division Head',
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',
            'PMO',
            'Project Manager',
            'Team Leader Engineering',
            'Team Leader',
            'Lead Divisi',
            'Lead Maintenance',
            'Lead Engineer',
            'Managed Service',
        ]);
    }

    /**
     * Apakah user berada di cabang Commercial & Solution?
     */
    public static function isCommercial($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Group Leader Commercial & Solution',
            'Sales',
            'Account Manager',
            'BusDev',
            'BDM',
            'Business Development',
            'CRO',
            'Customer Relation Officer',
            'Presales',
            'Pre-Sales',
            'Solution Architect',
            'Solutions Architect',
            'Tech Develop',
            'Tech.Develp (R&D)',
            'R&D',
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
     */
    public static function canCreateProjects($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Director',
            'Direktur',
            'HD / Direktur',
            'Division Head',
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',
            'PMO',
            'Project Manager',
            'Lead Divisi',
            'Team Leader Engineering',
            'Team Leader',
            'Lead Engineer',
            'Sales',
            'Account Manager',
            'BusDev',
            'BDM',
            'Presales',
            'Solution Architect',
        ]);
    }

    /**
     * Apakah user memiliki wewenang operasional untuk mengelola task/tiket (buat & edit task)?
     */
    public static function canManageTasks($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Director',
            'Direktur',
            'HD / Direktur',
            'Division Head',
            'Group Leader',
            'Group Leader Delivery & Operation',
            'Team Leader Engineering',
            'Team Leader',
            'Lead Divisi',
            'Lead Maintenance',
            'Lead Engineer',
            'Managed Service',
        ]);
    }

    /**
     * Apakah user memiliki wewenang operasional (proyek/task)?
     */
    public static function canManageProjectsAndTasks($user): bool
    {
        return self::canManageTasks($user);
    }

    /**
     * Ambil daftar ID user yang berada dalam scope wewenang user yang login.
     * Digunakan untuk query filter task, schedule, presensi, dll.
     *
     * @return array|null null = akses semua user (Global), array = ID user dalam scope
     */
    public static function getScopeUserIds($user): ?array
    {
        if (!$user) return [];

        // 1. Director, Division Head, Group Leader, PMO -> Akses SELURUH tim & seluruh engineer
        if (self::isGlobal($user)) {
            return null;
        }

        // 2. Managed Service Coordinator / Helpdesk -> Pemantauan menyeluruh untuk tiket & dispatch
        if ($user->hasAnyRole(['Managed Service', 'Lead Maintenance'])) {
            return null;
        }

        // 3. Team Leader (Network Leader / Security Leader) -> Akses SEMUA engineer di divisinya
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

        // 4. Engineer / Field Staff -> Hanya dirinya sendiri
        return [$user->id];
    }

    /**
     * Terapkan scope query berdasarkan kolom user_id/engineer_id.
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
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAssignableEngineers($user)
    {
        $operationalRoles = [
            'Team Leader Engineering',
            'Team Leader',
            'Lead Maintenance',
            'Lead Engineer',
            'Network Engineer',
            'Security Engineer',
            'Managed Service',
            'Field Support (EOS)',
            'Field Support',
            'Engineer',
            'Maintenance',
            'Engineer L1',
            'Engineer L2',
        ];

        // 1. Director, Division Head, Group Leader, PMO, Managed Service Coordinator
        // -> Dapat menugaskan (dispatch) ke SEMUA personel teknis
        if (self::isGlobal($user) || $user->hasAnyRole(['Managed Service', 'Lead Maintenance'])) {
            return \App\Models\User::whereHas('roles', function($q) use ($operationalRoles) {
                $q->whereIn('name', $operationalRoles);
            })->active()->get();
        }

        // 2. Leader Divisi Teknis -> Assign ke personel di divisinya + dirinya sendiri
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

        // 3. Sales / BDM / Commercial -> Dapat menugaskan jadwal meeting/POC ke Presales, Solution Architect, dan Tim Engineer
        if ($user->hasAnyRole(['Sales', 'Account Manager', 'BusDev', 'BDM', 'Business Development', 'CRO', 'Customer Relation Officer'])) {
            $commercialAssignableRoles = array_merge($operationalRoles, [
                'Presales',
                'Pre-Sales',
                'Solution Architect',
                'Solutions Architect',
                'Tech Develop',
                'Tech.Develp (R&D)',
                'R&D',
                'Sales',
                'Account Manager',
                'BDM',
            ]);
            return \App\Models\User::whereHas('roles', function($q) use ($commercialAssignableRoles) {
                $q->whereIn('name', $commercialAssignableRoles);
            })->active()->get();
        }

        // 4. Presales & Engineer -> Hanya dirinya sendiri (read-only)
        return collect([$user]);
    }

    /**
     * Apakah user memiliki wewenang untuk membuat, mengedit, atau menghapus jadwal?
     * (Managerial & Sales/BDM bisa mengatur jadwal dengan Presales/Engineer; Presales & Engineer read-only)
     */
    public static function canManageSchedules($user): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole([
            'Director',
            'Direktur',
            'HD / Direktur',
            'Division Head',
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',
            'PMO',
            'Project Manager',
            'Lead Divisi',
            'Team Leader Engineering',
            'Team Leader',
            'Lead Maintenance',
            'Lead Engineer',
            'Managed Service',
            'Sales',
            'Account Manager',
            'BusDev',
            'BDM',
            'Business Development',
            'CRO',
            'Customer Relation Officer',
            'Solution Architect',
            'Solutions Architect',
            'SA',
            'Presales',
            'Pre-Sales',
        ]);
    }

    /**
     * Role apa saja yang dapat dibuat oleh user yang sedang login?
     */
    public static function getCreatableRoles($user): array
    {
        if (!$user) return [];

        if ($user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head'])) {
            return [
                'Director', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation',
                'PMO', 'Project Manager', 'Sales', 'BusDev', 'CRO', 'Presales', 'Solution Architect', 'Tech Develop',
                'Team Leader Engineering', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Field Support (EOS)'
            ];
        }

        if (self::isGroupLeader($user)) {
            return [
                'PMO', 'Project Manager', 'Sales', 'BusDev', 'CRO', 'Presales', 'Solution Architect', 'Tech Develop',
                'Team Leader Engineering', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Field Support (EOS)'
            ];
        }

        if (self::isTeamLeader($user)) {
            return ['Network Engineer', 'Security Engineer', 'Engineer'];
        }

        if ($user->hasAnyRole(['Managed Service', 'Lead Maintenance'])) {
            return ['Field Support (EOS)', 'Maintenance'];
        }

        return [];
    }

    /**
     * Role apa saja yang relevan ditampilkan di filter pencarian user?
     */
    public static function getFilterableRoles($user): array
    {
        if (!$user) return [];

        if ($user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader'])) {
            return [
                'Director', 'Division Head', 'Group Leader', 'PMO', 'Project Manager',
                'Sales', 'BusDev', 'CRO', 'Presales', 'Solution Architect', 'Tech Develop',
                'Team Leader Engineering', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Field Support (EOS)'
            ];
        }

        if (self::isTeamLeader($user)) {
            return ['Network Engineer', 'Security Engineer', 'Team Leader Engineering', 'Engineer L1', 'Engineer L2'];
        }

        return ['Network Engineer', 'Security Engineer', 'Field Support (EOS)', 'Engineer L1', 'Engineer L2'];
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

        // 1. Director & Division Head memiliki wewenang penuh
        if ($authUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head'])) {
            return true;
        }

        // Target adalah Director -> tidak ada yang boleh mengubah selain sesama Director
        if ($targetUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur'])) {
            return false;
        }

        // 2. Group Leader dapat mengelola jajaran di bawahnya
        if (self::isGroupLeader($authUser)) {
            if ($targetUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head'])) {
                return false;
            }
            return true;
        }

        // 3. Team Leader hanya dapat mengelola Engineer di divisinya sendiri
        if (self::isTeamLeader($authUser)) {
            if ($targetUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader', 'Lead Divisi', 'Team Leader', 'PMO'])) {
                return false;
            }
            if ($authUser->division_id && $targetUser->division_id === $authUser->division_id) {
                return true;
            }
            if ($authUser->team_id && $targetUser->team_id === $authUser->team_id) {
                return true;
            }
            return false;
        }

        // 4. Managed Service Coordinator hanya dapat mengelola staf Field Support
        if ($authUser->hasAnyRole(['Managed Service', 'Lead Maintenance'])) {
            return $targetUser->hasAnyRole(['Field Support (EOS)', 'Field Support', 'Maintenance']);
        }

        return false;
    }
}
