<?php
declare(strict_types=1);

namespace App\Services;

use App\Exports\BrandedReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportExportService
{
    public function __construct(private readonly SystemSettingsService $settingsService)
    {
    }

    /**
     * @param array{
     *   title?:string,
     *   columns?:array<int,array{key:string,label:string,numeric?:bool}>,
     *   filters?:array<string,string>,
     *   generated_by?:string
     * } $context
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse
     */
    public function export(string $filename, Collection $rows, string $format, array $context = [])
    {
        $format = strtolower($format);
        $columns = $context['columns'] ?? $this->inferColumns($rows);
        $title = (string) ($context['title'] ?? str($filename)->replace('_', ' ')->headline()->toString());
        $filters = $context['filters'] ?? [];
        $generatedBy = (string) ($context['generated_by'] ?? __('common.unknown'));
        $branding = $this->brandingData();
        $metadata = $this->exportMetadata($title, $filters, $generatedBy, $branding);
        $downloadName = $this->downloadFileName($filename, $format);

        if ($format === 'excel') {
            return Excel::download(new BrandedReportExport($rows, $columns, $branding, $metadata), $downloadName);
        }

        if ($format === 'csv') {
            $csv = $this->toCsv($rows, $columns, $metadata, $branding);

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$downloadName}",
            ]);
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.export-pdf', [
                'rows' => $rows,
                'columns' => $columns,
                'branding' => $branding,
                'metadata' => $metadata,
            ])->setPaper('a4', 'landscape');

            return $pdf->download($downloadName);
        }

        return response()->json([
            'title' => $title,
            'metadata' => $metadata,
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }

    /**
     * @param array<int, array{key:string,label:string,numeric?:bool}> $columns
     * @param array<string, mixed> $metadata
     * @param array<string, mixed> $branding
     */
    private function toCsv(Collection $rows, array $columns, array $metadata, array $branding): string
    {
        $lines = [];
        $filtersText = (string) ($metadata['filters_text'] ?? __('common.none'));
        $lines[] = $this->csvRow([(string) ($branding['system_name'] ?? 'ERP Platform')]);
        $lines[] = $this->csvRow([(string) ($branding['company_name'] ?? '')]);
        $lines[] = $this->csvRow([(string) ($metadata['title'] ?? __('reports.title'))]);
        $lines[] = $this->csvRow([__('reports.exported_at'), (string) ($metadata['exported_at'] ?? '')]);
        $lines[] = $this->csvRow([__('reports.generated_by'), (string) ($metadata['generated_by'] ?? '')]);
        $lines[] = $this->csvRow([__('reports.applied_filters'), $filtersText]);
        $lines[] = $this->csvRow([__('common.currency'), (string) ($branding['default_currency'] ?? '')]);
        $lines[] = '';

        $lines[] = $this->csvRow(array_map(static fn (array $column): string => $column['label'], $columns));
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $column) {
                $line[] = $this->formatValue($row->{$column['key']} ?? '', (bool) ($column['numeric'] ?? false));
            }
            $lines[] = $this->csvRow($line);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param array<int, mixed> $values
     */
    private function csvRow(array $values): string
    {
        return implode(',', array_map(static fn ($value): string => '"'.str_replace('"', '""', (string) $value).'"', $values));
    }

    /**
     * @return array<int, array{key:string,label:string,numeric?:bool}>
     */
    private function inferColumns(Collection $rows): array
    {
        if ($rows->isEmpty()) {
            return [];
        }

        $first = (array) $rows->first();
        $columns = [];
        foreach (array_keys($first) as $key) {
            $columns[] = [
                'key' => (string) $key,
                'label' => str((string) $key)->replace('_', ' ')->headline()->toString(),
            ];
        }

        return $columns;
    }

    /**
     * @param array<string, string> $filters
     * @return array<string, mixed>
     */
    private function exportMetadata(string $title, array $filters, string $generatedBy, array $branding): array
    {
        $dateFormat = (string) ($branding['date_format'] ?? 'Y-m-d');
        $exportedAt = now()->format($dateFormat.' H:i');
        $filterParts = [];
        foreach ($filters as $label => $value) {
            if (trim($value) === '') {
                continue;
            }
            $filterParts[] = "{$label}: {$value}";
        }

        return [
            'title' => $title,
            'generated_by' => $generatedBy,
            'exported_at' => $exportedAt,
            'filters_text' => $filterParts !== [] ? implode(', ', $filterParts) : __('common.none'),
            'currency' => (string) ($branding['default_currency'] ?? 'USD'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function brandingData(): array
    {
        $settings = $this->settingsService->getUiPayload();
        $logoPath = (string) ($settings['system_logo'] ?? '');
        $absoluteLogoPath = $logoPath !== '' ? Storage::disk('public')->path($logoPath) : '';

        return [
            'system_name' => (string) ($settings['system_name'] ?? config('app.name', 'ERP Platform')),
            'company_name' => (string) ($settings['company_name'] ?? ''),
            'company_email' => (string) ($settings['company_email'] ?? ''),
            'company_phone' => (string) ($settings['company_phone'] ?? ''),
            'default_currency' => (string) ($settings['default_currency'] ?? 'USD'),
            'date_format' => (string) ($settings['date_format'] ?? 'Y-m-d'),
            'logo_url' => (string) ($settings['logo_url'] ?? $settings['system_logo_url'] ?? ''),
            'logo_path' => $absoluteLogoPath !== '' && is_file($absoluteLogoPath) ? $absoluteLogoPath : '',
            'logo_data_uri' => $this->watermarkDataUri($absoluteLogoPath),
            'watermark_data_uri' => $this->watermarkDataUri($absoluteLogoPath),
        ];
    }

    private function watermarkDataUri(string $absoluteLogoPath): ?string
    {
        if ($absoluteLogoPath === '' || !is_file($absoluteLogoPath)) {
            return null;
        }

        $contents = @file_get_contents($absoluteLogoPath);
        if ($contents === false) {
            return null;
        }

        $mime = mime_content_type($absoluteLogoPath) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }

    private function formatValue(mixed $value, bool $numeric): string
    {
        if ($numeric && is_numeric($value)) {
            return number_format((float) $value, 2, '.', '');
        }

        return (string) $value;
    }

    private function downloadFileName(string $baseName, string $format): string
    {
        $slug = str($baseName)->replace('_', '-')->lower()->toString();

        return sprintf('%s-%s.%s', $slug, now()->format('Y-m-d'), $format === 'excel' ? 'xlsx' : $format);
    }
}
