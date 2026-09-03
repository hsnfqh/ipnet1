<?php

namespace App\Services;

use App\Models\Schedule;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ScheduleExportService
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

    public function generate(Collection $schedules, ?string $engineerFilterName = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // Hapus sheet bawaan
        $spreadsheet->removeSheetByIndex(0);

        // 1. Tab Daily / Harian
        $sheetDaily = $spreadsheet->createSheet();
        $sheetDaily->setTitle('Jadwal Harian');
        $this->buildDailySheet($sheetDaily, $schedules, $engineerFilterName);

        // 2. Tab Weekly / Mingguan
        $sheetWeekly = $spreadsheet->createSheet();
        $sheetWeekly->setTitle('Jadwal Mingguan');
        $this->buildWeeklySheet($sheetWeekly, $schedules, $engineerFilterName);

        // 3. Tab Monthly / Bulanan
        $sheetMonthly = $spreadsheet->createSheet();
        $sheetMonthly->setTitle('Jadwal Bulanan');
        $this->buildMonthlySheet($sheetMonthly, $schedules, $engineerFilterName);

        // Kembalikan ke tab aktif pertama (Harian)
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildDailySheet(Worksheet $sheet, Collection $schedules, ?string $engineerFilterName): void
    {
        $sheet->setShowGridLines(true);

        // Banner Judul
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN JADWAL KERJA HARIAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC81E2C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Sub Metadata
        $sheet->setCellValue('A2', 'Filter Engineer: ' . ($engineerFilterName ?: 'Semua Engineer'));
        $sheet->setCellValue('A3', 'Tanggal Cetak: ' . Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB');
        $sheet->setCellValue('A4', 'Total Jadwal: ' . $schedules->count() . ' Data');

        $metaStyle = [
            'font' => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF475569']],
        ];
        $sheet->getStyle('A2:A4')->applyFromArray($metaStyle);

        // Header Kolom Table
        $headers = ['No', 'Tanggal', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Judul Jadwal', 'Project', 'Engineer', 'Lokasi', 'Deskripsi'];
        $row = 6;
        $sheet->getRowDimension($row)->setRowHeight(26);

        foreach ($headers as $colIdx => $headerText) {
            $colLetter = chr(65 + $colIdx);
            $sheet->setCellValue($colLetter . $row, $headerText);
        }

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10.5],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF94A3B8']]],
        ];
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($headerStyle);

        // Data Rows urut tanggal & jam mulai
        $sortedSchedules = $schedules->sortBy([
            ['date', 'asc'],
            ['start_time', 'asc'],
        ]);

        $startDataRow = 7;
        $currentRow = $startDataRow;
        $no = 1;

        foreach ($sortedSchedules as $sch) {
            $sheet->getRowDimension($currentRow)->setRowHeight(22);

            $dateObj = $sch->date ? Carbon::parse($sch->date) : null;
            $tglStr = $dateObj ? $dateObj->format('d/m/Y') : '-';
            $hariStr = $dateObj ? ($this->daysIndo[$dateObj->format('l')] ?? $dateObj->format('l')) : '-';

            $startTimeStr = $sch->category === 'Day Off' ? ($sch->start_time ? substr($sch->start_time, 0, 5) : 'Seharian') : ($sch->start_time ? substr($sch->start_time, 0, 5) : '-');
            $endTimeStr = $sch->end_time ? substr($sch->end_time, 0, 5) : '-';

            $projName = $sch->category === 'Day Off' ? 'Day Off / Libur' : ($sch->project->name ?? '-');
            $engName = ($sch->relationLoaded('engineers') && $sch->engineers->isNotEmpty()) ? $sch->engineers->pluck('name')->join(', ') : ($sch->engineer->name ?? '-');

            $sheet->setCellValue('A' . $currentRow, $no);
            $sheet->setCellValue('B' . $currentRow, $tglStr);
            $sheet->setCellValue('C' . $currentRow, $hariStr);
            $sheet->setCellValue('D' . $currentRow, $startTimeStr);
            $sheet->setCellValue('E' . $currentRow, $endTimeStr);
            $sheet->setCellValue('F' . $currentRow, $sch->title ?: '-');
            $sheet->setCellValue('G' . $currentRow, $projName);
            $sheet->setCellValue('H' . $currentRow, $engName);
            $sheet->setCellValue('I' . $currentRow, $sch->location ?: '-');
            $sheet->setCellValue('J' . $currentRow, $sch->description ?: '-');

            // Striping warna
            $bgColor = ($no % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$currentRow}:E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $no++;
            $currentRow++;
        }

        if ($sortedSchedules->isEmpty()) {
            $sheet->mergeCells("A{$currentRow}:J{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", 'Tidak ada data jadwal.');
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['italic' => true, 'color' => ['argb' => 'FF64748B']],
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(24);
        }

        $this->autoFitColumns($sheet, 10);
    }

    private function buildWeeklySheet(Worksheet $sheet, Collection $schedules, ?string $engineerFilterName): void
    {
        $sheet->setShowGridLines(true);

        // Banner Judul
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN JADWAL KERJA MINGGUAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC81E2C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Sub Metadata
        $sheet->setCellValue('A2', 'Filter Engineer: ' . ($engineerFilterName ?: 'Semua Engineer'));
        $sheet->setCellValue('A3', 'Tanggal Cetak: ' . Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB');
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF475569']],
        ]);

        // Group jadwal berdasarkan Tahun & Minggu
        $groupedByWeek = $schedules->groupBy(function ($sch) {
            if (!$sch->date) return 'Tanpa Tanggal';
            $c = Carbon::parse($sch->date);
            return $c->format('Y-\WW');
        });

        $currentRow = 5;

        foreach ($groupedByWeek as $weekKey => $weekSchedules) {
            if ($weekKey === 'Tanpa Tanggal') {
                $weekTitle = 'Jadwal Tanpa Tanggal';
            } else {
                $firstSch = $weekSchedules->first();
                $c = Carbon::parse($firstSch->date);
                $startOfWeek = $c->copy()->startOfWeek()->format('d/m/Y');
                $endOfWeek = $c->copy()->endOfWeek()->format('d/m/Y');
                $weekNum = $c->weekOfYear;
                $weekTitle = "Minggu ke-{$weekNum} ({$startOfWeek} s/d {$endOfWeek}) - Total: " . $weekSchedules->count() . ' Jadwal';
            }

            $sheet->mergeCells("A{$currentRow}:J{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", '  ' . $weekTitle);
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF1E293B']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(24);
            $currentRow++;

            // Header Kolom per Minggu
            $headers = ['No', 'Tanggal', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Judul Jadwal', 'Project', 'Engineer', 'Lokasi', 'Deskripsi'];
            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            foreach ($headers as $colIdx => $headerText) {
                $colLetter = chr(65 + $colIdx);
                $sheet->setCellValue($colLetter . $currentRow, $headerText);
            }
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 9.5],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF334155']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $currentRow++;

            $sortedWeekSchedules = $weekSchedules->sortBy([
                ['date', 'asc'],
                ['start_time', 'asc'],
            ]);

            $no = 1;
            foreach ($sortedWeekSchedules as $sch) {
                $sheet->getRowDimension($currentRow)->setRowHeight(20);

                $dateObj = $sch->date ? Carbon::parse($sch->date) : null;
                $tglStr = $dateObj ? $dateObj->format('d/m/Y') : '-';
                $hariStr = $dateObj ? ($this->daysIndo[$dateObj->format('l')] ?? $dateObj->format('l')) : '-';
                $startTimeStr = $sch->start_time ? substr($sch->start_time, 0, 5) : '-';
                $endTimeStr = $sch->end_time ? substr($sch->end_time, 0, 5) : '-';

                $sheet->setCellValue('A' . $currentRow, $no);
                $sheet->setCellValue('B' . $currentRow, $tglStr);
                $sheet->setCellValue('C' . $currentRow, $hariStr);
                $sheet->setCellValue('D' . $currentRow, $startTimeStr);
                $sheet->setCellValue('E' . $currentRow, $endTimeStr);
                $sheet->setCellValue('F' . $currentRow, $sch->title ?: '-');
                $sheet->setCellValue('G' . $currentRow, $sch->project->name ?? '-');
                $sheet->setCellValue('H' . $currentRow, $sch->engineer->name ?? '-');
                $sheet->setCellValue('I' . $currentRow, $sch->location ?: '-');
                $sheet->setCellValue('J' . $currentRow, $sch->description ?: '-');

                $bgColor = ($no % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
                $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $no++;
                $currentRow++;
            }

            $currentRow++; // Baris spasi antar minggu
        }

        if ($schedules->isEmpty()) {
            $sheet->mergeCells("A5:J5");
            $sheet->setCellValue("A5", 'Tidak ada data jadwal.');
            $sheet->getStyle("A5")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['italic' => true, 'color' => ['argb' => 'FF64748B']],
            ]);
        }

        $this->autoFitColumns($sheet, 10);
    }

    private function buildMonthlySheet(Worksheet $sheet, Collection $schedules, ?string $engineerFilterName): void
    {
        $sheet->setShowGridLines(true);

        // Banner Judul
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN JADWAL KERJA BULANAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC81E2C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Sub Metadata
        $sheet->setCellValue('A2', 'Filter Engineer: ' . ($engineerFilterName ?: 'Semua Engineer'));
        $sheet->setCellValue('A3', 'Tanggal Cetak: ' . Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB');
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF475569']],
        ]);

        // Group jadwal berdasarkan Bulan
        $groupedByMonth = $schedules->groupBy(function ($sch) {
            if (!$sch->date) return 'Tanpa Tanggal';
            $c = Carbon::parse($sch->date);
            return $c->format('Y-m');
        });

        // Ringkasan Bulanan
        $sheet->setCellValue('A5', 'RINGKASAN JADWAL PER BULAN');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A6', 'Bulan');
        $sheet->setCellValue('B6', 'Jumlah Jadwal');
        $sheet->setCellValue('C6', 'Jumlah Project');
        $sheet->setCellValue('D6', 'Jumlah Engineer');

        $sheet->getStyle('A6:D6')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 9.5],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(22);

        $summaryRow = 7;
        foreach ($groupedByMonth as $monthKey => $mSchedules) {
            if ($monthKey === 'Tanpa Tanggal') {
                $monthLabel = 'Tanpa Tanggal';
            } else {
                $c = Carbon::parse($monthKey . '-01');
                $monthLabel = ($this->monthsIndo[$c->month] ?? $c->format('F')) . ' ' . $c->year;
            }

            $projectCount = $mSchedules->pluck('project_id')->filter()->unique()->count();
            $engineerCount = $mSchedules->pluck('engineer_id')->filter()->unique()->count();

            $sheet->setCellValue('A' . $summaryRow, $monthLabel);
            $sheet->setCellValue('B' . $summaryRow, $mSchedules->count());
            $sheet->setCellValue('C' . $summaryRow, $projectCount);
            $sheet->setCellValue('D' . $summaryRow, $engineerCount);

            $bgColor = ($summaryRow % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
            $sheet->getStyle("A{$summaryRow}:D{$summaryRow}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("B{$summaryRow}:D{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($summaryRow)->setRowHeight(20);

            $summaryRow++;
        }

        $currentRow = $summaryRow + 2;

        // Detail Rincian per Bulan
        foreach ($groupedByMonth as $monthKey => $mSchedules) {
            if ($monthKey === 'Tanpa Tanggal') {
                $monthTitle = 'Jadwal Tanpa Tanggal';
            } else {
                $c = Carbon::parse($monthKey . '-01');
                $monthTitle = 'Bulan: ' . ($this->monthsIndo[$c->month] ?? $c->format('F')) . ' ' . $c->year . ' (' . $mSchedules->count() . ' Jadwal)';
            }

            $sheet->mergeCells("A{$currentRow}:J{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", '  ' . $monthTitle);
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF1E293B']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(24);
            $currentRow++;

            // Header Kolom
            $headers = ['No', 'Tanggal', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Judul Jadwal', 'Project', 'Engineer', 'Lokasi', 'Deskripsi'];
            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            foreach ($headers as $colIdx => $headerText) {
                $colLetter = chr(65 + $colIdx);
                $sheet->setCellValue($colLetter . $currentRow, $headerText);
            }
            $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 9.5],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF334155']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $currentRow++;

            $sortedMSchedules = $mSchedules->sortBy([
                ['date', 'asc'],
                ['start_time', 'asc'],
            ]);

            $no = 1;
            foreach ($sortedMSchedules as $sch) {
                $sheet->getRowDimension($currentRow)->setRowHeight(20);

                $dateObj = $sch->date ? Carbon::parse($sch->date) : null;
                $tglStr = $dateObj ? $dateObj->format('d/m/Y') : '-';
                $hariStr = $dateObj ? ($this->daysIndo[$dateObj->format('l')] ?? $dateObj->format('l')) : '-';
                $startTimeStr = $sch->start_time ? substr($sch->start_time, 0, 5) : '-';
                $endTimeStr = $sch->end_time ? substr($sch->end_time, 0, 5) : '-';

                $sheet->setCellValue('A' . $currentRow, $no);
                $sheet->setCellValue('B' . $currentRow, $tglStr);
                $sheet->setCellValue('C' . $currentRow, $hariStr);
                $sheet->setCellValue('D' . $currentRow, $startTimeStr);
                $sheet->setCellValue('E' . $currentRow, $endTimeStr);
                $sheet->setCellValue('F' . $currentRow, $sch->title ?: '-');
                $sheet->setCellValue('G' . $currentRow, $sch->project->name ?? '-');
                $sheet->setCellValue('H' . $currentRow, $sch->engineer->name ?? '-');
                $sheet->setCellValue('I' . $currentRow, $sch->location ?: '-');
                $sheet->setCellValue('J' . $currentRow, $sch->description ?: '-');

                $bgColor = ($no % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
                $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $no++;
                $currentRow++;
            }

            $currentRow++;
        }

        if ($schedules->isEmpty()) {
            $sheet->mergeCells("A9:J9");
            $sheet->setCellValue("A9", 'Tidak ada data jadwal.');
            $sheet->getStyle("A9")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['italic' => true, 'color' => ['argb' => 'FF64748B']],
            ]);
        }

        $this->autoFitColumns($sheet, 10);
    }

    private function autoFitColumns(Worksheet $sheet, int $maxColIndex): void
    {
        $minWidths = [
            'A' => 6,   // No
            'B' => 14,  // Tanggal
            'C' => 12,  // Hari
            'D' => 12,  // Jam Mulai
            'E' => 12,  // Jam Selesai
            'F' => 26,  // Judul
            'G' => 22,  // Project
            'H' => 22,  // Engineer
            'I' => 20,  // Lokasi
            'J' => 32,  // Deskripsi
        ];

        for ($i = 0; $i < $maxColIndex; $i++) {
            $colLetter = chr(65 + $i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        foreach ($minWidths as $col => $minW) {
            $currentW = $sheet->getColumnDimension($col)->getWidth();
            if ($currentW < $minW) {
                $sheet->getColumnDimension($col)->setAutoSize(false);
                $sheet->getColumnDimension($col)->setWidth($minW);
            }
        }
    }

    /**
     * Generate PDF Document for Schedules
     */
    public function generatePdf(Collection $schedules, ?string $engineerFilterName = null)
    {
        $sortedSchedules = $schedules->sortBy([
            ['date', 'asc'],
            ['start_time', 'asc']
        ]);

        $totalSchedules  = $schedules->count();
        $uniqueProjects  = $schedules->pluck('project_id')->filter()->unique()->count();
        $uniqueEngineers = $schedules->pluck('engineer_id')->filter()->unique()->count();
        $uniqueDays      = $schedules->pluck('date')->map(function($d) {
            return $d instanceof Carbon ? $d->format('Y-m-d') : (string) $d;
        })->unique()->count();

        $data = [
            'schedules'          => $sortedSchedules,
            'engineerFilterName' => $engineerFilterName ?: 'Semua Engineer',
            'totalSchedules'     => $totalSchedules,
            'uniqueProjects'     => $uniqueProjects,
            'uniqueEngineers'    => $uniqueEngineers,
            'uniqueDays'         => $uniqueDays,
            'daysIndo'           => $this->daysIndo,
            'generatedAt'        => Carbon::now()->isoFormat('D MMMM Y, HH:mm') . ' WIB',
            'printedBy'          => auth()->user()?->name ?? 'Lead Engineer / Administrator',
        ];

        $pdf = Pdf::loadView('schedules.pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }
}
