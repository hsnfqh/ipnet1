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
    // Koordinat kantor PT IP Network Solusindo
    const OFFICE_LAT = -6.1664;
    const OFFICE_LON = 106.8148;
    const RADIUS_METER = 100;

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
            'note'            => $request->note,
            'attendance_date' => $today,
        ]);

        return response()->json([
            'message'      => 'Clock In berhasil!',
            'attendance'   => $attendance,
            'within_range' => $withinRange,
            'distance'     => $distance,
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
            'note'            => $request->note,
            'attendance_date' => $today,
        ]);

        // Hitung durasi kerja
        $duration = Carbon::parse($clockIn->created_at)->diffInMinutes(Carbon::parse($attendance->created_at));
        $hours   = intdiv($duration, 60);
        $minutes = $duration % 60;

        return response()->json([
            'message'    => 'Clock Out berhasil!',
            'attendance' => $attendance,
            'duration'   => "{$hours} jam {$minutes} menit",
        ]);
    }

    // ─── Lead Engineer: Rekap Presensi ───────────────────────────────────────
    public function recap(Request $request)
    {
        $engineers = User::role(['Engineer L1', 'Engineer L2'])->orderBy('name')->get();

        // Default: hari ini
        $date = $request->get('date', Carbon::today()->toDateString());
        $month = $request->get('month', Carbon::today()->format('Y-m'));

        // Rekap harian
        $dailyAttendances = Attendance::with('user')
            ->where('attendance_date', $date)
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        // Rekap bulanan
        [$year, $mon] = explode('-', $month);
        $monthlyAttendances = Attendance::with('user')
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $mon)
            ->where('type', 'clock_in')
            ->orderBy('attendance_date')
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
        $date = $request->get('date', Carbon::today()->toDateString());

        $rows = Attendance::with('user')
            ->where('attendance_date', $date)
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->map(fn($a) => [
                'id'             => $a->id,
                'user_id'        => $a->user_id,
                'name'           => $a->user->name ?? '-',
                'type'           => $a->type,
                'time'           => Carbon::parse($a->created_at)->format('H:i'),
                'distance'       => $a->distance_meters,
                'is_within_range'=> $a->is_within_range,
                'photo_url'      => $a->photo_path ? Storage::url($a->photo_path) : null,
                'note'           => $a->note,
            ]);

        return response()->json($rows);
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
