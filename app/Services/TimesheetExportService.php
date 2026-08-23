<?php

namespace App\Services;

use App\Models\Timesheet;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TimesheetExportService
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

    /**
     * Generate Excel Spreadsheet
     */
    public function generateExcel(Collection $timesheets, array $filters = []): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        // Sheet 1: Detail Timesheet
        $sheetDetail = $spreadsheet->createSheet();
        $sheetDetail->setTitle('Log Kerja Detail');
        $this->buildDetailSheet($sheetDetail, $timesheets, $filters);

        // Sheet 2: Ringkasan per Engineer
        $sheetSummary = $spreadsheet->createSheet();
        $sheetSummary->setTitle('Ringkasan Engineer');
        $this->buildSummarySheet($sheetSummary, $timesheets, $filters);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildDetailSheet(Worksheet $sheet, Collection $timesheets, array $filters): void
    {
        $sheet->setShowGridLines(true);

        // Header Title Banner
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN TIMESHEET / LOG AKTIVITAS KERJA');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'C81E2C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Metadata Subheaders
        $engineerText = !empty($filters['engineer_name']) ? $filters['engineer_name'] : 'Semua Engineer';
        $projectText  = !empty($filters['project_name']) ? $filters['project_name'] : 'Semua Project';
        $periodText   = !empty($filters['period_text']) ? $filters['period_text'] : 'Semua Waktu';

        $sheet->setCellValue('A2', "Filter Engineer : {$engineerText}");
        $sheet->setCellValue('A3', "Filter Project  : {$projectText}");
        $sheet->setCellValue('A4', "Periode         : {$periodText} | Dicetak: " . Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB');

        $metaStyle = [
            'font' => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF475569']],
        ];
        $sheet->getStyle('A2:A4')->applyFromArray($metaStyle);

        // Table Headers
        $headers = [
            'No',
            'Tanggal',
            'Hari',
            'Engineer',
            'Project',
            'Task',
            'Jam Kerja',
            'Durasi (Jam)',
            'Kategori',
            'Uraian Pekerjaan / Aktivitas'
        ];

        $row = 6;
        $sheet->getRowDimension($row)->setRowHeight(26);

        foreach ($headers as $colIdx => $text) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . $row, $text);
        }

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10.5],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF94A3B8']]],
        ];
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($headerStyle);

        // Rows
        $sorted = $timesheets->sortBy([
            ['date', 'asc'],
            ['start_time', 'asc'],
        ]);

        $currentRow = 7;
        $no = 1;
        $totalMinutes = 0;

        foreach ($sorted as $ts) {
            $sheet->getRowDimension($currentRow)->setRowHeight(22);

            $dateObj = $ts->date ? Carbon::parse($ts->date) : null;
            $tglStr  = $dateObj ? $dateObj->format('d/m/Y') : '-';
            $hariStr = $dateObj ? ($this->daysIndo[$dateObj->format('l')] ?? $dateObj->format('l')) : '-';

            $startTimeStr = $ts->start_time ? substr($ts->start_time, 0, 5) : '-';
            $endTimeStr   = $ts->end_time ? substr($ts->end_time, 0, 5) : '-';
            $timeRangeStr = "{$startTimeStr} - {$endTimeStr}";

            $durationHours = round($ts->duration_minutes / 60, 2);
            $totalMinutes += $ts->duration_minutes;

            $sheet->setCellValue('A' . $currentRow, $no);
            $sheet->setCellValue('B' . $currentRow, $tglStr);
            $sheet->setCellValue('C' . $currentRow, $hariStr);
            $sheet->setCellValue('D' . $currentRow, $ts->user?->name ?? '-');
            $sheet->setCellValue('E' . $currentRow, $ts->project?->name ?? '-');
            $sheet->setCellValue('F' . $currentRow, $ts->task?->title ?? '-');
            $sheet->setCellValue('G' . $currentRow, $timeRangeStr);
            $sheet->setCellValue('H' . $currentRow, $durationHours);
            $sheet->setCellValue('I' . $currentRow, $ts->category ?? 'On-Site');
            $sheet->setCellValue('J' . $currentRow, $ts->activity ?? '-');

            $bgColor = ($no % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $no++;
            $currentRow++;
        }

        if ($sorted->isEmpty()) {
            $sheet->mergeCells("A{$currentRow}:J{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", 'Tidak ada data log timesheet pada periode ini.');
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['italic' => true, 'color' => ['argb' => 'FF64748B']],
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(24);
            $currentRow++;
        } else {
            // Total Summary Row
            $sheet->mergeCells("A{$currentRow}:G{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", 'TOTAL JAM KERJA');
            $sheet->setCellValue("H{$currentRow}", round($totalMinutes / 60, 2));
            $sheet->setCellValue("I{$currentRow}", round($totalMinutes / 60, 1) . ' Jam');
            $sheet->setCellValue("J{$currentRow}", "Total: {$sorted->count()} Log Entri");

            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10.5, 'color' => ['argb' => 'FF1E293B']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF94A3B8']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($currentRow)->setRowHeight(26);
        }

        $minWidths = [
            'A' => 6,   // No
            'B' => 13,  // Tanggal
            'C' => 12,  // Hari
            'D' => 22,  // Engineer
            'E' => 22,  // Project
            'F' => 22,  // Task
            'G' => 15,  // Jam Kerja
            'H' => 14,  // Durasi (Jam)
            'I' => 14,  // Kategori
            'J' => 40,  // Uraian
        ];

        foreach ($minWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
    }

    private function buildSummarySheet(Worksheet $sheet, Collection $timesheets, array $filters): void
    {
        $sheet->setShowGridLines(true);

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'RINGKASAN TOTAL JAM KERJA PER ENGINEER');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'C81E2C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $headers = ['No', 'Nama Engineer', 'Total Hari Kerja', 'Total Entri Log', 'Total Durasi (Jam)', 'Rata-rata Jam/Hari'];
        $row = 3;
        $sheet->getRowDimension($row)->setRowHeight(24);

        foreach ($headers as $colIdx => $text) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . $row, $text);
        }

        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF94A3B8']]],
        ]);

        $grouped = $timesheets->groupBy('user_id');
        $currentRow = 4;
        $no = 1;

        foreach ($grouped as $userId => $userLogs) {
            $engineerName = $userLogs->first()->user?->name ?? 'User #' . $userId;
            $uniqueDays   = $userLogs->pluck('date')->unique()->count();
            $logCount     = $userLogs->count();
            $totalMins    = $userLogs->sum('duration_minutes');
            $totalHours   = round($totalMins / 60, 2);
            $avgHours     = $uniqueDays > 0 ? round($totalHours / $uniqueDays, 2) : 0;

            $sheet->setCellValue('A' . $currentRow, $no);
            $sheet->setCellValue('B' . $currentRow, $engineerName);
            $sheet->setCellValue('C' . $currentRow, $uniqueDays . ' Hari');
            $sheet->setCellValue('D' . $currentRow, $logCount . ' Log');
            $sheet->setCellValue('E' . $currentRow, $totalHours . ' Jam');
            $sheet->setCellValue('F' . $currentRow, $avgHours . ' Jam/Hari');

            $bgColor = ($no % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
            $sheet->getStyle("A{$currentRow}:F{$currentRow}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$currentRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $currentRow++;
            $no++;
        }

        if ($grouped->isEmpty()) {
            $sheet->mergeCells("A4:F4");
            $sheet->setCellValue("A4", 'Tidak ada data.');
            $sheet->getStyle("A4")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['italic' => true, 'color' => ['argb' => 'FF64748B']],
            ]);
        }

        $minWidths = ['A' => 6, 'B' => 28, 'C' => 18, 'D' => 16, 'E' => 20, 'F' => 20];
        foreach ($minWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
    }

    /**
     * Generate PDF Document
     */
    public function generatePdf(Collection $timesheets, array $filters = [])
    {
        $totalMinutes = $timesheets->sum('duration_minutes');
        $totalHours   = round($totalMinutes / 60, 2);
        $uniqueDays   = $timesheets->pluck('date')->unique()->count();
        $engineerCount = $timesheets->pluck('user_id')->unique()->count();

        $currentUser = auth()->user();
        $isDirektur    = $currentUser && $currentUser->hasAnyRole(['Direktur', 'HD / Direktur']);
        $isGroupLeader = $currentUser && \App\Helpers\ScopeHelper::isGroupLeader($currentUser);

        // Tentukan pembuat (Dibuat Oleh) & verifikator (Mengetahui & Menyetujui)
        if ($isDirektur) {
            // Jika Direktur yang export: Langsung 1 tanda tangan Direktur Utama (tanpa Dibuat Oleh)
            $showMaker        = false;
            $makerName        = null;
            $makerPosition    = null;
            $verifierName     = $currentUser->name ?? 'Hariyadi';
            $verifierPosition = 'Direktur Utama';
        } elseif ($isGroupLeader) {
            // Jika Susanto (Group Leader) yang export: Dibuat Susanto, Diketahui Hariyadi
            $showMaker        = true;
            $makerName        = $currentUser->name ?? 'Susanto Djaya';
            $makerPosition    = 'Group Leader';
            $verifierName     = 'Hariyadi';
            $verifierPosition = 'Direktur Utama';
        } else {
            // Jika Team Leader (Nugraha / Ignatius) yang export: Dibuat TL, Diketahui Susanto
            $showMaker        = true;
            $makerName        = $currentUser->name ?? 'Team Leader';
            $makerPosition    = $currentUser->position ?? ($currentUser->hasRole('Team Leader') ? 'Team Leader' : 'Lead Engineer');
            $verifierName     = 'Susanto Djaya';
            $verifierPosition = 'Group Leader';
        }

        $data = [
            'timesheets'       => $timesheets->sortBy([['date', 'asc'], ['start_time', 'asc']]),
            'filters'          => $filters,
            'totalHours'       => $totalHours,
            'uniqueDays'       => $uniqueDays,
            'engineerCount'    => $engineerCount,
            'generatedAt'      => Carbon::now()->isoFormat('D MMMM Y, HH:mm'),
            'showMaker'        => $showMaker,
            'makerName'        => $makerName,
            'makerPosition'    => $makerPosition,
            'verifierName'     => $verifierName,
            'verifierPosition' => $verifierPosition,
        ];

        $pdf = Pdf::loadView('timesheets.pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }
}
