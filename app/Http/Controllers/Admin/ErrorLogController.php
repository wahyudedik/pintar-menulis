<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorLogController extends Controller
{
    private string $logPath;

    // Max bytes to read from end of file (5 MB)
    private const MAX_READ_BYTES = 5 * 1024 * 1024;

    public function __construct()
    {
        $this->logPath = storage_path('logs/laravel.log');
    }

    public function index(Request $request)
    {
        $level   = (string) $request->get('level', 'all');
        if ($level === '') $level = 'all';
        
        $search  = (string) $request->get('search', '');
        $perPage = (int) $request->get('per_page', 50);
        $page    = (int) $request->get('page', 1);

        $entries = $this->parseLog($level, $search);
        $total   = count($entries);
        $entries = array_slice($entries, ($page - 1) * $perPage, $perPage);

        $stats      = $this->getStats();
        $totalPages = (int) ceil($total / $perPage);

        return view('admin.error-logs.index', compact(
            'entries', 'total', 'totalPages', 'page',
            'level', 'search', 'perPage', 'stats'
        ));
    }

    public function clear()
    {
        if (File::exists($this->logPath)) {
            File::put($this->logPath, '');
        }
        return back()->with('success', 'Log berhasil dikosongkan.');
    }

    public function download()
    {
        if (!File::exists($this->logPath)) {
            return back()->with('error', 'File log tidak ditemukan.');
        }
        return response()->download($this->logPath, 'laravel-' . now()->format('Y-m-d') . '.log');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Read only the tail of the log file to avoid memory exhaustion on large files.
     */
    private function readLogTail(): string
    {
        if (!File::exists($this->logPath)) {
            return '';
        }

        $size = File::size($this->logPath);
        if ($size === 0) return '';

        $readBytes = min($size, self::MAX_READ_BYTES);
        $handle    = fopen($this->logPath, 'rb');
        fseek($handle, -$readBytes, SEEK_END);
        $content = fread($handle, $readBytes);
        fclose($handle);

        // Drop the first (possibly partial) line
        $firstNewline = strpos($content, "\n");
        if ($firstNewline !== false && $readBytes < $size) {
            $content = substr($content, $firstNewline + 1);
        }

        return $content;
    }

    private function parseLog(string $level = 'all', string $search = ''): array
    {
        $content = $this->readLogTail();
        if (!$content) return [];

        $rawEntries = preg_split('/\n(?=\[\d{4}-\d{2}-\d{2})/', trim($content));
        if (!$rawEntries) return [];

        $entries = [];
        foreach (array_reverse($rawEntries) as $raw) {
            $raw = trim($raw);
            if (!$raw) continue;

            $entry = $this->parseEntry($raw);
            if (!$entry) continue;

            if ($level !== 'all' && strtolower($entry['level']) !== strtolower($level)) continue;
            if ($search && !str_contains(strtolower($entry['full']), strtolower($search))) continue;

            $entries[] = $entry;
        }

        return $entries;
    }

    private function parseEntry(string $raw): ?array
    {
        if (!preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+\w+\.(\w+):\s+(.*)$/s', $raw, $m)) {
            return null;
        }

        $level   = strtolower($m[2]);
        $rest    = trim($m[3]);
        $context = null;

        // Try to split message from JSON context
        $jsonStart = strpos($rest, ' {"');
        if ($jsonStart !== false) {
            $message    = substr($rest, 0, $jsonStart);
            $jsonStr    = substr($rest, $jsonStart + 1);
            $context    = $this->tryDecodeJson($jsonStr);
        } else {
            $message = $rest;
        }

        // Extract exception message from context
        if (is_array($context) && isset($context['exception'])) {
            $exMsg = $this->extractExceptionMessage((string) $context['exception']);
            if ($exMsg) $message = $exMsg;
        }

        return [
            'datetime'   => $m[1],
            'level'      => $level,
            'message'    => substr(trim($message), 0, 500),
            'context'    => $context,
            'stacktrace' => $this->extractStacktrace($raw),
            'full'       => substr($raw, 0, 2000),
        ];
    }

    private function extractExceptionMessage(string $str): ?string
    {
        if (preg_match('/\(code:\s*\d+\):\s*(.+?)\s+at\s+/s', $str, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function extractStacktrace(string $raw): ?string
    {
        if (preg_match('/"stacktrace":\s*"(.+?)(?:"}|"\s*})/s', $raw, $m)) {
            return str_replace('\n', "\n", $m[1]);
        }
        if (preg_match('/\[stacktrace\](.*?)(?=\[previous|$)/s', $raw, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function tryDecodeJson(string $str): mixed
    {
        $decoded = json_decode($str, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $str;
    }

    private function getStats(): array
    {
        // For stats, parse only a smaller tail to keep it fast
        $content = $this->readLogTail();
        $rawEntries = $content ? preg_split('/\n(?=\[\d{4}-\d{2}-\d{2})/', trim($content)) : [];

        $counts = ['error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0, 'critical' => 0, 'other' => 0];

        foreach ($rawEntries as $raw) {
            if (!preg_match('/^\[\d{4}-\d{2}-\d{2}[^\]]+\]\s+\w+\.(\w+):/i', trim($raw), $m)) continue;
            $l = strtolower($m[1]);
            if (isset($counts[$l])) $counts[$l]++;
            else $counts['other']++;
        }

        $counts['total'] = array_sum(array_values($counts));

        $counts['file_size'] = File::exists($this->logPath)
            ? $this->formatBytes(File::size($this->logPath))
            : '0 B';

        $counts['last_modified'] = File::exists($this->logPath)
            ? date('d M Y H:i:s', File::lastModified($this->logPath))
            : '-';

        return $counts;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
