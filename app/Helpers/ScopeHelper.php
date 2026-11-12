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
            'Kepala Divisi',
            'Head of Division',
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',
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
            'Kepala Divisi',
            'Head of Division',
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',
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
            'Kepala Divisi',
            'Head of Division',
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
            'Lead Engineer Network',
            'Lead Security',
            'Lead Maintenance',
            'Managed Service',
        ]);
    }

    /**
     * Apakah user adalah divisi Maintenance / Lead Maintenance?
     */
    public static function isMaintenance($user): bool
    {
        if (!$user) return false;
        if ($user->division_id == 3) return true;
        if ($user->hasAnyRole(['Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)'])) return true;
        if (isset($user->division) && stripos($user->division->name ?? '', 'maintenance') !== false) return true;
        return false;
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
            'Kepala Divisi',
            'Head of Division',
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
            'Lead Engineer Network',
            'Lead Security',
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
        $email = strtolower($user->email ?? '');
        $pos = strtolower($user->position ?? '');
        return $user->hasAnyRole(['PMO', 'Project Manager'])
            || str_contains($email, 'rizki')
            || str_contains($email, 'kuncoro')
            || str_contains($pos, 'project manager')
            || str_contains($pos, 'pmo');
    }

    /**
     * Dapatkan daftar kategori aktivitas engineer yang diizinkan untuk dilihat / dikelola oleh user:
     * - Head / Executive (Director, Division Head, Group Leader, Super Admin): Bisa melihat SEMUA ('project', 'managed_service', 'help_desk')
     * - PMO / Project Manager: Hanya bisa melihat 'project'
     * - Managed Service & Helpdesk (Maintenance, Field Support): Bisa melihat 'managed_service' dan 'help_desk'
     * - Lead Engineer: Sesuai divisinya masing-masing:
     *     - Divisi Maintenance/Helpdesk: 'managed_service' dan 'help_desk'
     *     - Divisi Network/Security: 'project'
     * - Field Engineer lainnya: Sesuai divisinya masing-masing.
     *
     * @param \App\Models\User|null $user
     * @return array<string>
     */
    public static function getAllowedActivityCategories($user): array
    {
        if (!$user) return [];

        // 1. Khusus Managed Service / Maintenance (Eris, Doris, Maryo) -> HANYA 'managed_service' & 'help_desk'
        // Prioritaskan check ini agar staf & lead MS tidak tercampur dengan proyek implementasi!
        if (self::isMaintenance($user) && !self::isExecutive($user)) {
            return ['managed_service', 'help_desk'];
        }

        // 2. Head of Division / Director / Executive -> Akses penuh (bisa lihat semua kategori)
        if (self::isExecutive($user) || self::isGroupLeader($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Kepala Divisi', 'Head of Division', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation'])) {
            return ['project', 'managed_service', 'help_desk'];
        }

        // 3. PMO / Project Manager -> Khusus implementasi project
        if (self::isPmo($user)) {
            return ['project'];
        }

        // 4. Tim Teknis Engineer (Network & Security) -> Khusus implementasi project
        return ['project'];
    }

    /**
     * Periksa apakah user memiliki hak akses untuk kategori aktivitas tertentu.
     */
    public static function canAccessActivityCategory($user, ?string $category): bool
    {
        if (!$category) return true;
        $allowed = self::getAllowedActivityCategories($user);
        return in_array($category, $allowed);
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
            'Sales',
            'Account Manager',
            'BusDev',
            'BDM',
            'Business Development',
            'Presales',
            'Pre-Sales',
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

        // 1.5. Tim Maintenance -> Akses bersama 3 personel tim maintenance (Doris, Mario, Eris)
        if (self::isMaintenance($user)) {
            return \App\Models\User::where(function($q) {
                $q->where('division_id', 3)
                  ->orWhereHas('roles', fn($r) => $r->whereIn('name', ['Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)']))
                  ->orWhereIn('name', ['Doris', 'Mario', 'Eris']);
            })->active()->pluck('id')->toArray();
        }

        // 2. Team Leader (Lead Network, Lead Security, Lead Maintenance, Lead Engineer) -> Akses hanya divisi sendiri
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

        // 3. Engineer / Field Staff / Maintenance Staff -> Hanya dirinya sendiri
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
            'Lead Engineer',
            'Network Engineer',
            'Security Engineer',
            'Engineer',
            'Engineer L1',
            'Engineer L2',
        ];

        // 1. Director, Division Head, Group Leader, PMO -> Akses Global
        if (self::isGlobal($user)) {
            return \App\Models\User::whereHas('roles', function($q) use ($operationalRoles) {
                $q->whereIn('name', array_merge($operationalRoles, ['Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support']));
            })->active()->get();
        }

        // 2. Tim Maintenance -> Hanya 3 personel tim maintenance sendiri (Doris, Mario, Eris / Divisi 3)
        if (self::isMaintenance($user)) {
            return \App\Models\User::where(function($q) {
                $q->where('division_id', 3)
                  ->orWhereHas('roles', fn($rq) => $rq->whereIn('name', ['Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)']))
                  ->orWhereIn('name', ['Doris', 'Mario', 'Eris']);
            })->active()->orderBy('name')->get();
        }

        // 3. Leader Divisi Teknis (Lead Engineer / Team Leader) -> Assign ke personel di divisinya sendiri + dirinya sendiri
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
            return collect([$user]);
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
        if (self::isMaintenance($user)) return true;
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
     * Hanya Director dan Head of Division yang berhak membuat user baru.
     */
    public static function getCreatableRoles($user): array
    {
        if (!$user) return [];

        if (self::isExecutive($user)) {
            return [
                'Director', 'Division Head', 'Kepala Divisi', 'Head of Division',
                'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation',
                'PMO', 'Project Manager', 'Sales', 'Account Manager', 'BusDev', 'BDM', 'CRO',
                'Presales', 'Lead Pre Sales', 'Solution Architect', 'Tech Develop',
                'Team Leader Engineering', 'Lead Engineer Network', 'Lead Security',
                'Lead Maintenance', 'Network Engineer', 'Security Engineer', 'Managed Service',
                'Field Support (EOS)', 'Field Support', 'Admin Support', 'Warehouse'
            ];
        }

        return [];
    }

    /**
     * Role apa saja yang relevan ditampilkan di filter pencarian user?
     */
    public static function getFilterableRoles($user): array
    {
        if (!$user) return [];

        if ($user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Kepala Divisi', 'Head of Division', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager'])) {
            return [
                'Admin Support',
                'BDM',
                'Director',
                'Kepala Divisi',
                'Lead Engineer Network',
                'Lead Security',
                'Managed Service',
                'Network Engineer',
                'Pre Sales',
                'Project Manager',
                'Sales',
                'Security Engineer',
                'Solution Architect',
                'Warehouse',
            ];
        }

        if (self::isTeamLeader($user)) {
            return [
                'Network Engineer',
                'Security Engineer',
                'Managed Service',
                'Lead Engineer Network',
                'Lead Security',
            ];
        }

        return ['Network Engineer', 'Security Engineer', 'Managed Service'];
    }

    /**
     * Apakah auth user berhak mengelola (edit, toggle status, hapus) target user?
     * Hanya Director dan Head of Division yang berhak mengelola pengguna lainnya.
     */
    public static function canManageUser($authUser, $targetUser): bool
    {
        if (!$authUser || !$targetUser) return false;

        // Tidak dapat mengubah status atau menghapus akun sendiri
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        // HANYA Director dan Head of Division yang memiliki hak akses mengelola user lain
        if (self::isExecutive($authUser)) {
            // Target adalah Director -> hanya sesama Director yang berhak mengelola
            if ($targetUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']) && !$authUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur'])) {
                return false;
            }
            return true;
        }

        // Role lainnya (Team Leader, Lead Maintenance, Managed Service, PMO, Engineer, dll.) TIDAK punya hak mengelola pengguna lainnya
        return false;
    }
}
