<?php
// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Koordinat kantor PT IP Network Solusindo (Jl. Majapahit No.26P)
    const OFFICE_LAT = -6.1716;
    const OFFICE_LON = 106.8169;
    const RADIUS_METER = 500;

    // ─── Engineer: halaman Clock In / Out ───────────────────────────────────
    public function index()
    {
        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        // Data presensi hari ini milik user
        $todayAttendances = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->orderBy('created_at')
            ->get();

        $clockIn  = $todayAttendances->firstWhere('type', 'clock_in');
        $clockOut = $todayAttendances->firstWhere('type', 'clock_out');

        // Auto-sinkronisasi status radius jika sebelumnya tercatat di bawah 500 meter
        if ($clockIn && !$clockIn->is_within_range && $clockIn->distance_meters !== null && $clockIn->distance_meters <= self::RADIUS_METER) {
            $clockIn->is_within_range = true;
            try { $clockIn->save(); } catch (\Exception $e) {}
        }
        if ($clockOut && !$clockOut->is_within_range && $clockOut->distance_meters !== null && $clockOut->distance_meters <= self::RADIUS_METER) {
            $clockOut->is_within_range = true;
            try { $clockOut->save(); } catch (\Exception $e) {}
        }

        // Riwayat 7 hari terakhir
        $history = Attendance::where('user_id', $user->id)
            ->where('attendance_date', '>=', Carbon::today()->subDays(6)->toDateString())
            ->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('attendance_date');

        return view('attendance.index', compact('clockIn', 'clockOut', 'history', 'today'));
    }

    // ─── Engineer: Clock In ──────────────────────────────────────────────────
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo'     => 'nullable|string', // base64
            'note'      => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:500',
        ]);

        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        // Cek apakah sudah clock in hari ini
        $existing = Attendance::where('user_id', $user->id)
            ->where('type', 'clock_in')
            ->where('attendance_date', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah Clock In hari ini.'], 422);
        }

        $lat = (float) $request->latitude;
        $lon = (float) $request->longitude;

        // Hitung jarak dari kantor
        $distance = Attendance::haversineDistance(self::OFFICE_LAT, self::OFFICE_LON, $lat, $lon);
        $withinRange = $distance <= self::RADIUS_METER;

        // Tentukan alamat
        $address = $request->address;
        if (empty($address) && $withinRange) {
            $address = 'Kantor Pusat PT IP Network Solusindo (Jl. Majapahit No.26P)';
        }

        // Simpan foto selfie jika ada
        $photoPath = null;
        if ($request->photo) {
            $photoPath = $this->saveBase64Photo($request->photo, $user->id, 'clock_in');
        }

        $attendance = Attendance::create([
            'user_id'         => $user->id,
            'type'            => 'clock_in',
            'latitude'        => $lat,
            'longitude'       => $lon,
            'distance_meters' => $distance,
            'is_within_range' => $withinRange,
            'photo_path'      => $photoPath,
            'address'         => $address,
            'note'            => $request->note,
            'attendance_date' => $today,
        ]);

        return response()->json([
            'message'      => 'Clock In berhasil!',
            'attendance'   => $attendance,
            'within_range' => $withinRange,
            'distance'     => $distance,
            'address'      => $attendance->address,
            'time'         => Carbon::parse($attendance->created_at)->setTimezone('Asia/Jakarta')->format('H:i'),
            'date'         => Carbon::parse($attendance->created_at)->setTimezone('Asia/Jakarta')->format('d M Y'),
        ]);
    }

    // ─── Engineer: Clock Out ─────────────────────────────────────────────────
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo'     => 'nullable|string',
            'note'      => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:500',
        ]);

        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        // Harus sudah clock in dulu
        $clockIn = Attendance::where('user_id', $user->id)
            ->where('type', 'clock_in')
            ->where('attendance_date', $today)
            ->first();

        if (!$clockIn) {
            return response()->json(['message' => 'Anda belum Clock In hari ini.'], 422);
        }

        // Cek sudah clock out
        $existing = Attendance::where('user_id', $user->id)
            ->where('type', 'clock_out')
            ->where('attendance_date', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah Clock Out hari ini.'], 422);
        }

        $lat = (float) $request->latitude;
        $lon = (float) $request->longitude;

        $distance    = Attendance::haversineDistance(self::OFFICE_LAT, self::OFFICE_LON, $lat, $lon);
        $withinRange = $distance <= self::RADIUS_METER;

        // Tentukan alamat
        $address = $request->address;
        if (empty($address) && $withinRange) {
            $address = 'Kantor Pusat PT IP Network Solusindo (Jl. Majapahit No.26P)';
        }

        $photoPath = null;
        if ($request->photo) {
            $photoPath = $this->saveBase64Photo($request->photo, $user->id, 'clock_out');
        }

        $attendance = Attendance::create([
            'user_id'         => $user->id,
            'type'            => 'clock_out',
            'latitude'        => $lat,
            'longitude'       => $lon,
            'distance_meters' => $distance,
            'is_within_range' => $withinRange,
            'photo_path'      => $photoPath,
            'address'         => $address,
            'note'            => $request->note,
            'attendance_date' => $today,
        ]);

        // Hitung durasi kerja
        $duration = Carbon::parse($clockIn->created_at)->diffInMinutes(Carbon::parse($attendance->created_at));
        $hours   = intdiv($duration, 60);
        $minutes = $duration % 60;

        return response()->json([
            'message'        => 'Clock Out berhasil!',
            'attendance'     => $attendance,
            'duration'       => "{$hours} jam {$minutes} menit",
            'duration_short' => "{$hours}j {$minutes}m",
            'address'        => $attendance->address,
            'time'           => Carbon::parse($attendance->created_at)->setTimezone('Asia/Jakarta')->format('H:i'),
            'date'           => Carbon::parse($attendance->created_at)->setTimezone('Asia/Jakarta')->format('d M Y'),
        ]);
    }

    // ─── Lead / Managerial: Rekap Presensi ───────────────────────────────────
    public function recap(Request $request)
    {
        $user      = auth()->user();
        $scopeIds  = \App\Helpers\ScopeHelper::getScopeUserIds($user);
        $engineers = \App\Helpers\ScopeHelper::getAssignableEngineers($user);

        // Default: hari ini
        $date  = $request->get('date', Carbon::today()->toDateString());
        $month = $request->get('month', Carbon::today()->format('Y-m'));

        // Rekap harian
        $dailyAttendancesQuery = Attendance::with('user')
            ->where('attendance_date', $date);
        
        if ($scopeIds !== null) {
            $dailyAttendancesQuery->whereIn('user_id', $scopeIds);
        }

        $dailyAttendances = $dailyAttendancesQuery->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        // Rekap bulanan
        [$year, $mon] = explode('-', $month);
        $monthlyAttendancesQuery = Attendance::with('user')
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $mon)
            ->where('type', 'clock_in');

        if ($scopeIds !== null) {
            $monthlyAttendancesQuery->whereIn('user_id', $scopeIds);
        }

        $monthlyAttendances = $monthlyAttendancesQuery->orderBy('attendance_date')
            ->get()
            ->groupBy('user_id');

        return view('attendance.recap', compact(
            'engineers',
            'dailyAttendances',
            'monthlyAttendances',
            'date',
            'month'
        ));
    }

    // ─── API: data rekap harian (filter AJAX) ───────────────────────────────
    public function dailyData(Request $request)
    {
        $user     = auth()->user();
        $scopeIds = \App\Helpers\ScopeHelper::getScopeUserIds($user);
        $date     = $request->get('date', Carbon::today()->toDateString());

        $query = Attendance::with('user')
            ->where('attendance_date', $date);

        if ($scopeIds !== null) {
            $query->whereIn('user_id', $scopeIds);
        }

        $rows = $query->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->map(fn($a) => [
                'id'             => $a->id,
                'user_id'        => $a->user_id,
                'name'           => $a->user->name ?? '-',
                'type'           => $a->type,
                'time'           => Carbon::parse($a->created_at)->setTimezone('Asia/Jakarta')->format('H:i'),
                'distance'       => $a->distance_meters,
                'is_within_range'=> $a->is_within_range,
                'photo_url'      => $a->photo_path ? Storage::url($a->photo_path) : null,
                'address'        => $a->address,
                'note'           => $a->note,
            ]);

        return response()->json($rows);
    }

    // ─── Export PDF Rekap Presensi ──────────────────────────────────────────
    public function exportPdf(Request $request, \App\Services\AttendanceExportService $exportService)
    {
        try {
            $user  = auth()->user();
            $tab   = $request->get('tab', 'daily');
            $date  = $request->get('date', Carbon::today()->toDateString());
            $month = $request->get('month', Carbon::today()->format('Y-m'));

            $pdf = $exportService->generatePdf($tab, $date, $month, $user);

            $filename = $tab === 'monthly'
                ? 'Laporan_Presensi_Bulanan_' . str_replace('-', '_', $month) . '_' . date('Ymd_His') . '.pdf'
                : 'Laporan_Presensi_Harian_' . str_replace('-', '_', $date) . '_' . date('Ymd_His') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengeksport PDF Presensi: ' . $e->getMessage());
        }
    }

    // ─── Helper: simpan foto base64 ──────────────────────────────────────────
    private function saveBase64Photo(string $base64, int $userId, string $type): string
    {
        // Hapus prefix data:image/...;base64,
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $imageData = base64_decode($imageData);

        $filename  = "attendance/{$userId}_{$type}_" . now()->format('Ymd_His') . '.jpg';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }
}
