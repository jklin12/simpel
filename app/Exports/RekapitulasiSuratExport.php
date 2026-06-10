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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class RekapitulasiSuratExport implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private array $data;
    private int   $month;
    private int   $year;

    // Row index trackers for styling
    private int $headerRow      = 3;
    private array $groupRows  = [];   // ['kelurahan'|'kecamatan' => row_index]
    private int   $footerRow = 0;

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
        $kelurahans = $this->data['columns'];
        $rows       = $this->data['rows'];
        $totals     = $this->data['totals'];
        $grandTotal = $this->data['grand_total'];
        $monthLabel = self::$MONTHS[$this->month] ?? $this->month;

        $sheet = [];

        // Row 1: title
        $sheet[] = ["REKAPITULASI SURAT — {$monthLabel} {$this->year}"];
        // Row 2: blank
        $sheet[] = [];

        // Row 3: headers
        $headers = ['No', 'Jenis Surat', 'Kode'];
        foreach ($kelurahans as $kel) {
            $headers[] = $kel;
        }
        $headers[] = 'Total';
        $sheet[] = $headers;

        $currentRow = 4;
        $no = 1;

        $rowsKelurahan = array_values(array_filter($rows, fn($r) => !$r['requires_kecamatan']));
        $rowsKecamatan = array_values(array_filter($rows, fn($r) =>  $r['requires_kecamatan']));

        // Group: Diproses di Kelurahan
        if (!empty($rowsKelurahan)) {
            $sheet[] = ['', 'DIPROSES DI KELURAHAN', '', ...array_fill(0, count($kelurahans) + 1, '')];
            $this->groupRows['kelurahan'] = $currentRow++;

            foreach ($rowsKelurahan as $row) {
                $line = [$no++, $row['nama'], $row['kode']];
                foreach ($row['per_column'] as $count) {
                    $line[] = $count ?: 0;
                }
                $line[] = $row['total'];
                $sheet[] = $line;
                $currentRow++;
            }
        }

        // Group: Diproses di Kecamatan
        if (!empty($rowsKecamatan)) {
            $sheet[] = ['', 'DIPROSES DI KECAMATAN', '', ...array_fill(0, count($kelurahans) + 1, '')];
            $this->groupRows['kecamatan'] = $currentRow++;

            foreach ($rowsKecamatan as $row) {
                $line = [$no++, $row['nama'], $row['kode']];
                foreach ($row['per_column'] as $count) {
                    $line[] = $count ?: 0;
                }
                $line[] = $row['total'];
                $sheet[] = $line;
                $currentRow++;
            }
        }

        // Footer total row
        $footer = ['', 'TOTAL', ''];
        foreach ($totals as $t) {
            $footer[] = $t;
        }
        $footer[] = $grandTotal;
        $sheet[] = $footer;
        $this->footerRow = $currentRow;

        return $sheet;
    }

    public function title(): string
    {
        $monthLabel = self::$MONTHS[$this->month] ?? $this->month;
        return "Rekapitulasi {$monthLabel} {$this->year}";
    }

    public function styles(Worksheet $sheet): array
    {
        $styles = [
            // Title
            1 => [
                'font'      => ['bold' => true, 'size' => 13],
            ],
            // Header
            $this->headerRow => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            ],
            // Footer
            $this->footerRow => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            ],
        ];

        // Group header rows
        foreach ($this->groupRows as $type => $rowIdx) {
            $styles[$rowIdx] = [
                'font' => ['bold' => true, 'italic' => true,
                    'color' => ['rgb' => $type === 'kelurahan' ? '065F46' : '5B21B6']],
                'fill' => ['fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $type === 'kelurahan' ? 'D1FAE5' : 'EDE9FE']],
            ];
        }

        return $styles;
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 5, 'B' => 42, 'C' => 10];

        $kelCount = count($this->data['columns']);
        for ($i = 1; $i <= $kelCount + 1; $i++) {
            $col = Coordinate::stringFromColumnIndex(3 + $i);
            $widths[$col] = 20;
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws       = $event->sheet->getDelegate();
                $kelCount = count($this->data['columns']);
                $lastColIdx = 3 + $kelCount + 1;
                $lastCol    = Coordinate::stringFromColumnIndex($lastColIdx);
                $lastRow    = $this->footerRow;

                // Merge title across all columns
                $ws->mergeCells("A1:{$lastCol}1");

                // Right-align numeric columns (kelurahan columns + total)
                for ($row = $this->headerRow; $row <= $lastRow; $row++) {
                    for ($col = 4; $col <= $lastColIdx; $col++) {
                        $cellCoord = Coordinate::stringFromColumnIndex($col) . $row;
                        $ws->getStyle($cellCoord)->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                }

                // Merge group header label across all columns
                foreach ($this->groupRows as $groupRowIdx) {
                    $ws->mergeCells("A{$groupRowIdx}:{$lastCol}{$groupRowIdx}");
                }

                // Border around full table (header to footer)
                $ws->getStyle("A{$this->headerRow}:{$lastCol}{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Freeze header row
                $ws->freezePane('A' . ($this->headerRow + 1));

                // Alternate row shading for data rows (skip group headers and footer)
                $groupRowSet = array_flip($this->groupRows);
                $shade = false;
                for ($row = $this->headerRow + 1; $row < $lastRow; $row++) {
                    if (isset($groupRowSet[$row])) {
                        $shade = false; // reset alternation at each group
                        continue;
                    }
                    if ($shade) {
                        $ws->getStyle("A{$row}:{$lastCol}{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F0F9FF');
                    }
                    $shade = !$shade;
                }
            },
        ];
    }
}
