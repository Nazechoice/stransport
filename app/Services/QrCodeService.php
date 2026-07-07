<?php

declare(strict_types=1);

namespace Transport\Services;

final class QrCodeService
{
    public function renderSvg(string $payload, int $size = 220): string
    {
        $encoded = rawurlencode($payload);

        if (class_exists(\Endroid\QrCode\Builder\Builder::class) && class_exists(\Endroid\QrCode\Writer\SvgWriter::class)) {
            try {
                $result = \Endroid\QrCode\Builder\Builder::create()
                    ->writer(new \Endroid\QrCode\Writer\SvgWriter())
                    ->writerOptions([
                        \Endroid\QrCode\Writer\SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true,
                    ])
                    ->data($payload)
                    ->size($size)
                    ->margin(10)
                    ->build();

                return $result->getString();
            } catch (\Throwable) {
                // Fall back to a remote SVG QR response.
            }
        }

        $remote = 'https://api.qrserver.com/v1/create-qr-code/?format=svg&size=' . (int) $size . 'x' . (int) $size . '&data=' . $encoded;
        $svg = null;

        if (function_exists('file_get_contents')) {
            $svg = @file_get_contents($remote);
        }

        if (is_string($svg) && str_contains(ltrim($svg), '<svg')) {
            return preg_replace('/<\?xml.*?\?>\s*/', '', $svg, 1) ?? $svg;
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 ' . (int) $size . ' ' . (int) $size . '" role="img" aria-label="QR code unavailable"><rect width="100%" height="100%" fill="#ffffff"/><rect x="0" y="0" width="' . (int) $size . '" height="' . (int) $size . '" fill="#f8fafc" rx="18"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="#64748b">QR unavailable</text></svg>';
    }
}
