<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorLogController extends Controller
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs/laravel.log');
    }

    public function index(Request $request)
    {
        $level    = $request->get('level', 'all');
        $search   = $request->get('search', '');
        $perPage  = (int) $request->get('per_page', 50);
        $page     = (int) $request->get('page', 1);

        $entries  = $this->parseLog($level, $search);
        $total    = count($entries);
        $entries  = array_slice($entries, ($page - 1) * $perPage, $perPage);

        $stats = $this->getStats();

        if ($request->expectsJson()) {
            return response()->json(compact('entries', 'total', 'stats'));
        }

        $totalPages = (int) ceil($total / $perPage);

        return view('admin.error-logs.index', compact(
            'entries', 'total', 'totalPages', 'page',
            'level', 'search', 'perPage', 'stats'
        ));
    }

    public function clear(Request $request)
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

    private function parseLog(string $level = 'all', string $search = ''): array
    {
        if (!File::exists($this->logPath)) {
            return [];
        }

        $content = File::get($this->logPath);
        // Split on log entry boundaries
        $rawEntries = preg_split('/\n(?=\[\d{4}-\d{2}-\d{2})/', trim($content));

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
        // [2026-03-19 05:33:16] local.ERROR: message {"context"}
        if (!preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s+(.+?)(?:\s*(\{.*\}|\[.*\]))?\s*$/s', $raw, $m)) {
            // Try simpler match
            if (!preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+\w+\.(\w+):\s+(.*)$/s', $raw, $m2)) {
                return null;
            }
            return [
                'datetime'  => $m2[1],
                'level'     => strtolower($m2[2]),
                'message'   => trim(substr($m2[3], 0, 300)),
                'context'   => null,
                'stacktrace'=> $this->extractStacktrace($m2[3]),
                'full'      => $raw,
            ];
        }

        $message = trim($m[4]);
        $context = isset($m[5]) ? $this->tryDecodeJson($m[5]) : null;

        // Extract exception message from context
        if (is_array($context) && isset($context['exception'])) {
            $exMsg = $this->extractExceptionMessage($context['exception']);
            if ($exMsg) $message = $exMsg;
        }

        return [
            'datetime'   => $m[1],
            'level'      => strtolower($m[3]),
            'message'    => substr($message, 0, 500),
            'context'    => $context,
            'stacktrace' => $this->extractStacktrace($raw),
            'full'       => $raw,
        ];
    }

    private function extractExceptionMessage(string $exceptionStr): ?string
    {
        // "[object] (ExceptionClass(code: 0): Message at file:line)"
        if (preg_match('/\(code:\s*\d+\):\s*(.+?)\s+at\s+/s', $exceptionStr, $m)) {
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
        $all = $this->parseLog();
        $counts = ['error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0, 'critical' => 0, 'other' => 0];

        foreach ($all as $e) {
            $l = $e['level'];
            if (isset($counts[$l])) $counts[$l]++;
            else $counts['other']++;
        }

        $counts['total'] = count($all);

        // File size
        $counts['file_size'] = File::exists($this->logPath)
            ? $this->formatBytes(File::size($this->logPath))
            : '0 B';

        // Last modified
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
