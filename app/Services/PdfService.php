<?php

declare(strict_types=1);

namespace Transport\Services;

final class PdfService
{
    public function isAvailable(): bool
    {
        return class_exists(\Dompdf\Dompdf::class) || class_exists(\TCPDF::class);
    }

    public function downloadHtmlAsPdfLikeResponse(string $html, string $filename): void
    {
        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $dompdf->output();
            exit;
        }

        if (class_exists(\TCPDF::class)) {
            $pdf = new \TCPDF();
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $pdf->Output($filename, 'S');
            exit;
        }

        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.html"');
        echo $html;
        exit;
    }
}

