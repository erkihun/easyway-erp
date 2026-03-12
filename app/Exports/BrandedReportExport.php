<?php
declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BrandedReportExport implements FromArray, WithStyles, WithEvents, ShouldAutoSize, WithDrawings
{
    /**
     * @param array<int, array{key:string,label:string,numeric?:bool}> $columns
     * @param array<string, mixed> $branding
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private readonly Collection $rows,
        private readonly array $columns,
        private readonly array $branding,
        private readonly array $metadata,
    ) {
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = [(string) ($this->branding['system_name'] ?? 'ERP Platform')];
        $rows[] = [(string) ($this->branding['company_name'] ?? '')];
        $rows[] = [(string) ($this->metadata['title'] ?? 'Report')];
        $rows[] = [__('reports.exported_at').': '.(string) ($this->metadata['exported_at'] ?? '')];
        $rows[] = [__('reports.generated_by').': '.(string) ($this->metadata['generated_by'] ?? '')];
        $rows[] = [__('reports.applied_filters').': '.(string) ($this->metadata['filters_text'] ?? __('common.none'))];
        $rows[] = [];
        $rows[] = array_map(static fn (array $column): string => (string) $column['label'], $this->columns);

        foreach ($this->rows as $row) {
            $line = [];
            foreach ($this->columns as $column) {
                $line[] = $row->{$column['key']} ?? '';
            }
            $rows[] = $line;
        }

        return $rows;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function styles(Worksheet $sheet): array
    {
        $headerRow = 8;
        $lastColumn = $this->columnLetter(count($this->columns));

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->mergeCells("A3:{$lastColumn}3");
        $sheet->mergeCells("A4:{$lastColumn}4");
        $sheet->mergeCells("A5:{$lastColumn}5");
        $sheet->mergeCells("A6:{$lastColumn}6");

        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0F172A']]],
            2 => ['font' => ['size' => 11, 'color' => ['rgb' => '334155']]],
            3 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E293B']]],
            4 => ['font' => ['size' => 10, 'color' => ['rgb' => '475569']]],
            5 => ['font' => ['size' => 10, 'color' => ['rgb' => '475569']]],
            6 => ['font' => ['size' => 10, 'color' => ['rgb' => '475569']]],
            $headerRow => [
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{event:class-string, callable}>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $headerRow = 8;
                $firstDataRow = 9;
                $lastDataRow = max($firstDataRow, $firstDataRow + $this->rows->count() - 1);
                $lastColumn = $this->columnLetter(count($this->columns));

                $sheet->freezePane("A{$firstDataRow}");
                $sheet->setAutoFilter("A{$headerRow}:{$lastColumn}{$headerRow}");

                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$lastDataRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('E2E8F0');

                foreach ($this->columns as $index => $column) {
                    if (!($column['numeric'] ?? false)) {
                        continue;
                    }

                    $columnLetter = $this->columnLetter($index + 1);
                    $sheet->getStyle("{$columnLetter}{$firstDataRow}:{$columnLetter}{$lastDataRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');

                    $sheet->getStyle("{$columnLetter}{$headerRow}:{$columnLetter}{$lastDataRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                $sheet->getStyle("A1:A6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            },
        ];
    }

    /**
     * @return array<int, Drawing>|Drawing|array<empty>
     */
    public function drawings(): array|Drawing
    {
        $logoPath = (string) ($this->branding['logo_path'] ?? '');
        if ($logoPath === '' || !is_file($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Report Logo');
        $drawing->setDescription('System logo');
        $drawing->setPath($logoPath);
        $drawing->setCoordinates('A1');
        $drawing->setHeight(50);

        return [$drawing];
    }

    private function columnLetter(int $index): string
    {
        $index = max(1, $index);
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = intdiv($index - $mod - 1, 26);
        }

        return $letter;
    }
}
