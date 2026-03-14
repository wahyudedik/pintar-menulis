<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\UserTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TemplateImportExportController extends Controller
{
    // 📤 Export single template
    public function exportSingle($id)
    {
        $template = UserTemplate::where('user_id', Auth::id())
            ->findOrFail($id);

        $export = [
            'template_export' => [
                'version' => '1.0',
                'exported_at' => now()->toIso8601String(),
                'exported_by' => Auth::user()->email,
                'templates' => [
                    [
                        'title' => $template->title,
                        'description' => $template->description,
                        'category' => $template->category,
                        'platform' => $template->platform,
                        'tone' => $template->tone,
                        'template_content' => $template->template_content,
                        'format_instructions' => $template->format_instructions,
                        'tags' => $template->tags,
                        'license_type' => $template->license_type,
                        'metadata' => [
                            'author' => Auth::user()->name,
                            'created_at' => $template->created_at->toDateString(),
                            'usage_count' => $template->usage_count,
                            'rating_average' => $template->rating_average,
                        ]
                    ]
                ]
            ]
        ];

        $template->incrementDownload();

        $filename = 'template_' . \Str::slug($template->title) . '_' . date('Ymd') . '.json';

        return response()->json($export)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // 📤 Export multiple templates
    public function exportMultiple(Request $request)
    {
        $validated = $request->validate([
            'template_ids' => 'required|array',
            'template_ids.*' => 'exists:user_templates,id'
        ]);

        $templates = UserTemplate::where('user_id', Auth::id())
            ->whereIn('id', $validated['template_ids'])
            ->get();

        $exportData = [];
        foreach ($templates as $template) {
            $exportData[] = [
                'title' => $template->title,
                'description' => $template->description,
                'category' => $template->category,
                'platform' => $template->platform,
                'tone' => $template->tone,
                'template_content' => $template->template_content,
                'format_instructions' => $template->format_instructions,
                'tags' => $template->tags,
                'license_type' => $template->license_type,
                'metadata' => [
                    'author' => Auth::user()->name,
                    'created_at' => $template->created_at->toDateString(),
                ]
            ];

            $template->incrementDownload();
        }

        $export = [
            'template_export' => [
                'version' => '1.0',
                'exported_at' => now()->toIso8601String(),
                'exported_by' => Auth::user()->email,
                'total_templates' => count($exportData),
                'templates' => $exportData
            ]
        ];

        $filename = 'templates_bulk_' . date('Ymd_His') . '.json';

        return response()->json($export)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // 📤 Export all user templates
    public function exportAll()
    {
        $templates = UserTemplate::where('user_id', Auth::id())->get();

        $exportData = [];
        foreach ($templates as $template) {
            $exportData[] = [
                'title' => $template->title,
                'description' => $template->description,
                'category' => $template->category,
                'platform' => $template->platform,
                'tone' => $template->tone,
                'template_content' => $template->template_content,
                'format_instructions' => $template->format_instructions,
                'tags' => $template->tags,
                'is_public' => $template->is_public,
                'is_premium' => $template->is_premium,
                'price' => $template->price,
                'license_type' => $template->license_type,
                'metadata' => [
                    'author' => Auth::user()->name,
                    'created_at' => $template->created_at->toDateString(),
                    'usage_count' => $template->usage_count,
                    'rating_average' => $template->rating_average,
                ]
            ];
        }

        $export = [
            'template_export' => [
                'version' => '1.0',
                'exported_at' => now()->toIso8601String(),
                'exported_by' => Auth::user()->email,
                'total_templates' => count($exportData),
                'templates' => $exportData
            ]
        ];

        $filename = 'all_templates_' . Auth::id() . '_' . date('Ymd') . '.json';

        return response()->json($export)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // 📥 Import templates
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:10240' // Max 10MB
        ]);

        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($content, true);

            if (!isset($data['template_export'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file tidak valid. Pastikan file adalah export template yang valid.'
                ], 422);
            }

            $export = $data['template_export'];
            $templates = $export['templates'] ?? [];

            $imported = 0;
            $errors = [];

            foreach ($templates as $index => $templateData) {
                $validator = Validator::make($templateData, [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'category' => 'required|string',
                    'platform' => 'required|string',
                    'tone' => 'required|string',
                    'template_content' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Template #" . ($index + 1) . ": " . $validator->errors()->first();
                    continue;
                }

                // Check for duplicates
                $exists = UserTemplate::where('user_id', Auth::id())
                    ->where('title', $templateData['title'])
                    ->exists();

                if ($exists) {
                    $errors[] = "Template '{$templateData['title']}' sudah ada. Dilewati.";
                    continue;
                }

                // Create template
                UserTemplate::create([
                    'user_id' => Auth::id(),
                    'title' => $templateData['title'],
                    'description' => $templateData['description'],
                    'category' => $templateData['category'],
                    'platform' => $templateData['platform'],
                    'tone' => $templateData['tone'],
                    'template_content' => $templateData['template_content'],
                    'format_instructions' => $templateData['format_instructions'] ?? null,
                    'tags' => $templateData['tags'] ?? null,
                    'is_public' => $templateData['is_public'] ?? false,
                    'is_premium' => $templateData['is_premium'] ?? false,
                    'price' => $templateData['price'] ?? null,
                    'license_type' => $templateData['license_type'] ?? 'free',
                    'status' => 'draft',
                ]);

                $imported++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$imported} template" . (count($errors) > 0 ? " dengan " . count($errors) . " error" : ""),
                'imported_count' => $imported,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import template: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📥 Import from URL
    public function importFromUrl(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $content = file_get_contents($validated['url']);
            $data = json_decode($content, true);

            if (!isset($data['template_export'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format URL tidak valid.'
                ], 422);
            }

            // Process same as file import
            $export = $data['template_export'];
            $templates = $export['templates'] ?? [];

            $imported = 0;
            foreach ($templates as $templateData) {
                UserTemplate::create([
                    'user_id' => Auth::id(),
                    'title' => $templateData['title'],
                    'description' => $templateData['description'],
                    'category' => $templateData['category'],
                    'platform' => $templateData['platform'],
                    'tone' => $templateData['tone'],
                    'template_content' => $templateData['template_content'],
                    'format_instructions' => $templateData['format_instructions'] ?? null,
                    'tags' => $templateData['tags'] ?? null,
                    'status' => 'draft',
                ]);
                $imported++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$imported} template dari URL",
                'imported_count' => $imported
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import dari URL: ' . $e->getMessage()
            ], 500);
        }
    }
}
