<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use App\Helpers\ScopeHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceExportService
{
    private array $daysIndo = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    private array $monthsIndo = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Generate PDF Document for Attendance (Daily or Monthly).
     */
    public function generatePdf(string $tab, ?string $date, ?string $month, $currentUser)
    {
        $scopeIds  = ScopeHelper::getScopeUserIds($currentUser);
        $engineers = ScopeHelper::getAssignableEngineers($currentUser);

        $date  = $date ?: Carbon::today()->toDateString();
        $month = $month ?: Carbon::today()->format('Y-m');

        if ($tab === 'monthly') {
            return $this->generateMonthlyPdf($month, $engineers, $scopeIds, $currentUser);
        }

        return $this->generateDailyPdf($date, $engineers, $scopeIds, $currentUser);
    }

    /**
     * Generate Daily Attendance Report PDF.
     */
    private function generateDailyPdf(string $date, Collection $engineers, ?array $scopeIds, $currentUser)
    {
        $dateObj = Carbon::parse($date);
        $dateFormatted = $dateObj->isoFormat('D MMMM Y');
        $dayNameIndo   = $this->daysIndo[$dateObj->format('l')] ?? $dateObj->format('l');

        $attendancesQuery = Attendance::with('user')
            ->where('attendance_date', $date);

        if ($scopeIds !== null) {
            $attendancesQuery->whereIn('user_id', $scopeIds);
        }

        $allRecords = $attendancesQuery->orderBy('created_at')->get()->groupBy('user_id');

        $rows = [];
        $hadirCount = 0;
        $luarRadiusCount = 0;
        $belumHadirCount = 0;

        foreach ($engineers as $eng) {
            $userRecords = $allRecords->get($eng->id, collect());
            $clockIn  = $userRecords->firstWhere('type', 'clock_in');
            $clockOut = $userRecords->firstWhere('type', 'clock_out');

            $status = 'Belum Hadir';
            $statusColor = '#64748B';
            $inTime = '-';
            $outTime = '-';
            $duration = '-';
            $distance = '-';
            $address = '-';
            $note = '-';

            if ($clockIn) {
                $inTime = Carbon::parse($clockIn->created_at)->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB';
                $distance = $clockIn->distance_meters ? round($clockIn->distance_meters) . ' m' : '-';
                $address = $clockIn->address ?: ($clockOut?->address ?: '-');
                $note = $clockIn->note ?: ($clockOut?->note ?: '-');

                if ($clockIn->is_within_range) {
                    $status = 'Hadir (Sesuai Radius)';
                    $statusColor = '#059669';
                    $hadirCount++;
                } else {
                    $status = 'Hadir (Luar Radius)';
                    $statusColor = '#D97706';
                    $luarRadiusCount++;
                }

                if ($clockOut) {
                    $outTime = Carbon::parse($clockOut->created_at)->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB';
                    $diffMins = Carbon::parse($clockIn->created_at)->diffInMinutes(Carbon::parse($clockOut->created_at));
                    $h = intdiv($diffMins, 60);
                    $m = $diffMins % 60;
                    $duration = "{$h}j {$m}m";
                } else {
                    $outTime = 'Belum Clock-Out';
                }
            } else {
                $belumHadirCount++;
            }

            $rows[] = [
                'engineer_name' => $eng->name,
                'role'          => $eng->role_label ?? ($eng->roles->first()?->name ?? 'Engineer'),
                'division'      => $eng->division?->name ?? 'Operasional',
                'status'        => $status,
                'status_color'  => $statusColor,
                'clock_in'      => $inTime,
                'clock_out'     => $outTime,
                'duration'      => $duration,
                'distance'      => $distance,
                'address'       => $address,
                'note'          => $note,
            ];
        }

        $summary = [
            'total'        => count($engineers),
            'hadir'        => $hadirCount,
            'luar_radius'  => $luarRadiusCount,
            'belum_hadir'  => $belumHadirCount,
            'persentase'   => count($engineers) > 0 ? round((($hadirCount + $luarRadiusCount) / count($engineers)) * 100, 1) . '%' : '0%',
        ];

        $signatures = $this->resolveSignatures($currentUser);

        $data = array_merge([
            'type'          => 'daily',
            'title'         => 'LAPORAN REKAPITULASI PRESENSI HARIAN (DAILY)',
            'dateFormatted' => "{$dayNameIndo}, {$dateFormatted}",
            'summary'       => $summary,
            'rows'          => $rows,
            'generatedAt'   => Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB',
            'printedBy'     => $currentUser->name ?? 'Administrator',
        ], $signatures);

        $pdf = Pdf::loadView('attendance.pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }

    /**
     * Generate Monthly Attendance Report PDF.
     */
    private function generateMonthlyPdf(string $month, Collection $engineers, ?array $scopeIds, $currentUser)
    {
        [$year, $mon] = explode('-', $month);
        $monthObj = Carbon::createFromDate($year, $mon, 1);
        $monthLabel = ($this->monthsIndo[(int)$mon] ?? $monthObj->format('F')) . ' ' . $year;

        // Hitung hari kerja dalam bulan bersangkutan (Senin - Jumat)
        $daysInMonth = $monthObj->daysInMonth;
        $workdaysCount = 0;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dt = Carbon::createFromDate($year, $mon, $d);
            if (!$dt->isWeekend()) {
                $workdaysCount++;
            }
        }

        $monthlyAttendancesQuery = Attendance::with('user')
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $mon);

        if ($scopeIds !== null) {
            $monthlyAttendancesQuery->whereIn('user_id', $scopeIds);
        }

        $allMonthlyRecords = $monthlyAttendancesQuery->orderBy('attendance_date')->get()->groupBy('user_id');

        $rows = [];
        $totalHadirAccumulated = 0;

        foreach ($engineers as $eng) {
            $userRecords = $allMonthlyRecords->get($eng->id, collect());
            $clockIns = $userRecords->where('type', 'clock_in');
            $clockOuts = $userRecords->where('type', 'clock_out');

            $hadirDays = $clockIns->pluck('attendance_date')->unique()->count();
            $inRangeDays = $clockIns->where('is_within_range', true)->pluck('attendance_date')->unique()->count();
            $outRangeDays = $hadirDays - $inRangeDays;

            // Hitung total jam kerja
            $totalMins = 0;
            $dates = $clockIns->pluck('attendance_date')->unique();
            foreach ($dates as $dt) {
                $ci = $clockIns->firstWhere('attendance_date', $dt);
                $co = $clockOuts->firstWhere('attendance_date', $dt);
                if ($ci && $co) {
                    $totalMins += Carbon::parse($ci->created_at)->diffInMinutes(Carbon::parse($co->created_at));
                } elseif ($ci) {
                    $totalMins += 8 * 60; // Standard 8 jam
                }
            }

            $totalHours = round($totalMins / 60, 1);
            $rate = $workdaysCount > 0 ? round(($hadirDays / $workdaysCount) * 100, 1) : 0;

            $totalHadirAccumulated += $hadirDays;

            $rows[] = [
                'engineer_name' => $eng->name,
                'role'          => $eng->role_label ?? ($eng->roles->first()?->name ?? 'Engineer'),
                'division'      => $eng->division?->name ?? 'Operasional',
                'hadir_days'    => $hadirDays,
                'in_range_days' => $inRangeDays,
                'out_range_days'=> $outRangeDays,
                'total_hours'   => $totalHours . ' Jam',
                'rate'          => $rate . '%',
            ];
        }

        $avgRate = count($engineers) > 0 && $workdaysCount > 0
            ? round(($totalHadirAccumulated / (count($engineers) * $workdaysCount)) * 100, 1) . '%'
            : '0%';

        $summary = [
            'total_engineer'  => count($engineers),
            'workdays_count'  => $workdaysCount . ' Hari',
            'total_hadir_acc' => $totalHadirAccumulated . ' Kehadiran',
            'avg_rate'        => $avgRate,
        ];

        $signatures = $this->resolveSignatures($currentUser);

        $data = array_merge([
            'type'          => 'monthly',
            'title'         => 'LAPORAN REKAPITULASI PRESENSI BULANAN (MONTHLY)',
            'dateFormatted' => "Periode: {$monthLabel}",
            'summary'       => $summary,
            'rows'          => $rows,
            'workdaysCount' => $workdaysCount,
            'generatedAt'   => Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB',
            'printedBy'     => $currentUser->name ?? 'Administrator',
        ], $signatures);

        $pdf = Pdf::loadView('attendance.pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }

    /**
     * Tentukan pembuat (Dibuat Oleh) & verifikator (Mengetahui & Menyetujui) secara dinamis.
     */
    private function resolveSignatures($currentUser): array
    {
        $isDirektur    = $currentUser && $currentUser->hasAnyRole(['Direktur', 'HD / Direktur']);
        $isGroupLeader = $currentUser && ScopeHelper::isGroupLeader($currentUser);

        if ($isDirektur) {
            // Jika Direktur yang export: Langsung 1 tanda tangan Direktur Utama
            return [
                'showMaker'        => false,
                'makerName'        => null,
                'makerPosition'    => null,
                'verifierName'     => $currentUser->name ?? 'Hariyadi',
                'verifierPosition' => 'Direktur Utama',
            ];
        }

        if ($isGroupLeader) {
            // Jika Susanto (Group Leader) yang export: Dibuat Susanto, Diketahui Hariyadi
            return [
                'showMaker'        => true,
                'makerName'        => $currentUser->name ?? 'Susanto Djaya',
                'makerPosition'    => 'Group Leader',
                'verifierName'     => 'Hariyadi',
                'verifierPosition' => 'Direktur Utama',
            ];
        }

        // Jika Team Leader (Nugraha / Ignatius) yang export: Dibuat TL, Diketahui Susanto
        return [
            'showMaker'        => true,
            'makerName'        => $currentUser->name ?? 'Team Leader',
            'makerPosition'    => $currentUser->position ?? ($currentUser->hasRole('Team Leader') ? 'Team Leader' : 'Lead Engineer'),
            'verifierName'     => 'Susanto Djaya',
            'verifierPosition' => 'Group Leader',
        ];
    }
}
