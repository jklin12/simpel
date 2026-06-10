<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapitulasiSuratExport implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private array $data;
    private int   $month;
    private int   $year;

    private static array $MONTHS = [
        1  => 'Januari',  2  => 'Februari', 3  => 'Maret',    4  => 'April',
        5  => 'Mei',      6  => 'Juni',      7  => 'Juli',     8  => 'Agustus',
        9  => 'September',10 => 'Oktober',   11 => 'November', 12 => 'Desember',
    ];

    public function __construct(array $data, int $month, int $year)
    {
        $this->data  = $data;
        $this->month = $month;
        $this->year  = $year;
    }

    public function array(): array
    {
        $kecamatans = $this->data['kecamatans'];
        $rows       = $this->data['rows'];
        $totals     = $this->data['totals_per_kecamatan'];
        $grandTotal = $this->data['grand_total'];

        $monthLabel = self::$MONTHS[$this->month] ?? $this->month;

        // Row 1: title
        $sheet[] = ["REKAPITULASI SURAT — {$monthLabel} {$this->year}"];

        // Row 2: blank
        $sheet[] = [];

        // Row 3: headers
        $headers = ['No', 'Jenis Surat', 'Kode'];
        foreach ($kecamatans as $kec) {
            $headers[] = $kec;
        }
        $headers[] = 'Total';
        $sheet[] = $headers;

        // Data rows
        foreach ($rows as $i => $row) {
            $line = [$i + 1, $row['nama'], $row['kode']];
            foreach ($row['per_kecamatan'] as $count) {
                $line[] = $count ?: 0;
            }
            $line[] = $row['total'];
            $sheet[] = $line;
        }

        // Total footer row
        $footer = ['', 'TOTAL', ''];
        foreach ($totals as $t) {
            $footer[] = $t;
        }
        $footer[] = $grandTotal;
        $sheet[] = $footer;

        return $sheet;
    }

    public function title(): string
    {
        $monthLabel = self::$MONTHS[$this->month] ?? $this->month;
        return "Rekapitulasi {$monthLabel} {$this->year}";
    }

    public function styles(Worksheet $sheet): array
    {
        $kecCount   = count($this->data['kecamatans']);
        $lastColIdx = 3 + $kecCount + 1; // No + Nama + Kode + kecs + Total (1-indexed)
        $lastCol    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIdx);
        $dataRows   = count($this->data['rows']);
        $headerRow  = 3;
        $lastRow    = $headerRow + $dataRows + 1;

        return [
            // Title row: bold, large
            1 => [
                'font'      => ['bold' => true, 'size' => 13],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            // Header row: bold + background
            $headerRow => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            ],
            // Footer total row: bold + light background
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 5, 'B' => 40, 'C' => 10];

        $kecCount = count($this->data['kecamatans']);
        for ($i = 1; $i <= $kecCount + 1; $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $i);
            $widths[$col] = 18;
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws         = $event->sheet->getDelegate();
                $kecCount   = count($this->data['kecamatans']);
                $lastColIdx = 3 + $kecCount + 1;
                $lastCol    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIdx);
                $dataRows   = count($this->data['rows']);
                $headerRow  = 3;
                $lastRow    = $headerRow + $dataRows + 1;

                // Merge title across all columns
                $ws->mergeCells("A1:{$lastCol}1");

                // Number columns: right-align all numeric cells (kecamatan + total columns)
                for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
                    for ($col = 4; $col <= $lastColIdx; $col++) {
                        $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                        $ws->getStyle($cellCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                }

                // Border around data area
                $ws->getStyle("A{$headerRow}:{$lastCol}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Freeze header row
                $ws->freezePane("A" . ($headerRow + 1));

                // Alternate row shading for readability
                for ($row = $headerRow + 1; $row < $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $ws->getStyle("A{$row}:{$lastCol}{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F0F7FF');
                    }
                }
            },
        ];
    }
}
