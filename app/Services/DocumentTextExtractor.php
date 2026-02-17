<?php

namespace App\Services;

use App\Models\Document;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class DocumentTextExtractor
{
    public function extractFromDocument(Document $document): string
    {
        if (!empty(trim((string) $document->content))) {
            return $this->normalizeText($document->content);
        }

        $path = $this->resolveDocumentPath($document);
        $extension = strtolower((string) ($document->file_original_extension ?: pathinfo($path, PATHINFO_EXTENSION)));

        $text = match ($extension) {
            'docx' => $this->extractFromDocx($path),
            'txt' => $this->extractFromTxt($path),
            'pdf' => $this->extractFromPdf($path),
            default => throw new RuntimeException("Unsupported extension: {$extension}"),
        };

        $text = $this->normalizeText($text);

        if ($text === '') {
            throw new RuntimeException('No text could be extracted from the source document.');
        }

        return $text;
    }

    private function resolveDocumentPath(Document $document): string
    {
        $fileUrl = trim((string) $document->file_url);
        if ($fileUrl === '') {
            throw new RuntimeException('The document has no file path.');
        }

        if (Str::startsWith($fileUrl, ['http://', 'https://'])) {
            $path = parse_url($fileUrl, PHP_URL_PATH) ?: '';
            $fileUrl = preg_replace('#^/storage/#', '', $path);
        }

        $relativePath = ltrim($fileUrl, '/');
        $relativePath = preg_replace('#^storage/#', '', $relativePath);

        $candidates = [
            storage_path('app/public/' . $relativePath),
            storage_path('app/' . $relativePath),
            storage_path('app/private/' . $relativePath),
            public_path($relativePath),
            public_path('storage/' . $relativePath),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('Document file not found on disk.');
    }

    private function extractFromTxt(string $path): string
    {
        $text = @file_get_contents($path);
        if ($text === false) {
            throw new RuntimeException('Unable to read TXT file.');
        }

        return $text;
    }

    private function extractFromDocx(string $path): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new RuntimeException('ZipArchive extension is required to extract DOCX.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open DOCX archive.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new RuntimeException('DOCX content is unreadable.');
        }

        $xml = str_replace(['</w:p>', '</w:tr>', '</w:tbl>'], "\n", $xml);
        $xml = str_replace(['</w:tc>'], " ", $xml);
        $xml = preg_replace('/<w:tab[^>]*\/>/', "\t", $xml) ?? $xml;
        $xml = preg_replace('/<w:br[^>]*\/>/', "\n", $xml) ?? $xml;
        $text = strip_tags($xml);

        return $this->normalizeText($text);
    }

    private function extractFromPdf(string $path): string
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $pages = $pdf->getPages();

            $text = '';
            foreach ($pages as $page) {
                $text .= $page->getText() . "\n";
            }

            $text = $this->normalizeText($text);
            if ($text !== '' && $this->isMostlyReadable($text)) {
                return $text;
            }
        }
        catch (\Throwable $e) {
            Log::warning('PDF extraction failed.', [
                'message' => $e->getMessage(),
            ]);
        }

        throw new RuntimeException('Unable to extract readable text from PDF. Please use DOCX or TXT.');
    }

    private function normalizeText(string $text): string
    {
        if (!mb_check_encoding($text, 'UTF-8')) {
            $converted = @mb_convert_encoding($text, 'UTF-8', 'UTF-8,UTF-16LE,UTF-16BE,Windows-1252,ISO-8859-1');
            $text = $converted !== false ? $converted : $text;
        }

        $iconv = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
        if ($iconv !== false) {
            $text = $iconv;
        }

        $text = str_replace("\x00", '', $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $text) ?? $text;
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\n{2,}/', "\n", $text) ?? $text;

        return trim($text);
    }

    private function isMostlyReadable(string $text): bool
    {
        $length = mb_strlen($text);
        if ($length === 0) {
            return false;
        }

        $printable = preg_match_all('/[\p{L}\p{N}\p{P}\p{Zs}\n\t]/u', $text);
        if ($printable === false) {
            return false;
        }

        return ($printable / $length) >= 0.75;
    }
}
