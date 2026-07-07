<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Services\PdfService;
use Transport\Services\ReportService;

final class ReportController extends BaseController
{
    public function index(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $service = new ReportService();
        $summary = $service->summary();
        $stats = (new \Transport\Services\StatsService())->counts();
        $this->view('reports.index', compact('summary', 'stats'));
    }

    public function exportCsv(string $period): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $rows = (new ReportService())->revenueByPeriod($period);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . $period . '_' . date('Ymd_His') . '.csv"');
        $output = fopen('php://output', 'wb');
        fputcsv($output, ['Period', 'Revenue', 'Payments']);
        foreach ($rows as $row) {
            fputcsv($output, [$row['period_date'], $row['revenue'], $row['payments']]);
        }
        fclose($output);
        exit;
    }

    public function exportExcel(string $period): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $rows = (new ReportService())->revenueByPeriod($period);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="report_' . $period . '_' . date('Ymd_His') . '.xls"');
        echo "Period\tRevenue\tPayments\n";
        foreach ($rows as $row) {
            echo $row['period_date'] . "\t" . $row['revenue'] . "\t" . $row['payments'] . "\n";
        }
        exit;
    }

    public function exportPdf(string $period): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $rows = (new ReportService())->revenueByPeriod($period);
        ob_start();
        $this->view('reports.pdf', compact('rows', 'period'));
        $html = ob_get_clean() ?: '';
        (new PdfService())->downloadHtmlAsPdfLikeResponse($html, 'report_' . $period . '.pdf');
    }
}
