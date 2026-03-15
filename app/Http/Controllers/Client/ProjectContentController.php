<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectContent;
use App\Models\ContentComment;
use App\Models\ContentVersion;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectContentController extends Controller
{
    use AuthorizesRequests;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * List project content
     */
    public function index(Project $project)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses ke project ini.');
        }

        $query = $project->content()->with(['creator', 'reviewer']);

        // Apply filters
        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('type')) {
            $query->where('content_type', request('type'));
        }

        if (request('search')) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('content', 'like', '%' . request('search') . '%');
            });
        }

        if (request('created_by')) {
            $query->where('created_by', request('created_by'));
        }

        $contents = $query->latest()->paginate(20);

        $userRole = $project->getMemberRole(auth()->user());
        $canEdit = $project->canUserEdit(auth()->user());
        $canApprove = $project->canUserApprove(auth()->user());

        return view('projects.content.index', compact('project', 'contents', 'userRole', 'canEdit', 'canApprove'));
    }

    /**
     * Show create content form
     */
    public function create(Project $project)
    {
        if (!$project->canUserEdit(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk membuat content.');
        }

        return view('projects.content.create', compact('project'));
    }

    /**
     * Store new content
     */
    public function store(Request $request, Project $project)
    {
        if (!$project->canUserEdit(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk membuat content.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'platform' => 'nullable|string',
            'type' => 'required|string|in:caption,article,ad_copy,email,product_desc',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
            'action' => 'required|string|in:draft,review'
        ]);

        $status = $validated['action'] === 'review' ? 'review' : 'draft';

        // Prepare metadata
        $metadata = [];
        if (!empty($validated['tags'])) {
            $metadata['tags'] = $validated['tags'];
        }
        if (!empty($validated['notes'])) {
            $metadata['notes'] = $validated['notes'];
        }

        $content = $project->content()->create([
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'platform' => $validated['platform'],
            'content_type' => $validated['type'], // Map 'type' to 'content_type'
            'metadata' => $metadata,
            'status' => $status
        ]);

        // Create initial version
        $content->createVersion('created', 'Initial version');

        return redirect()->route('projects.content.show', [$project, $content])
            ->with('success', 'Content berhasil dibuat.');
    }

    /**
     * Show content detail
     */
    public function show(Project $project, ProjectContent $content)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses ke project ini.');
        }

        if ($content->project_id !== $project->id) {
            abort(404);
        }

        $content->load(['creator', 'reviewer', 'comments.user', 'comments.replies.user']);
        
        $userRole = $project->getMemberRole(auth()->user());
        $canEdit = $content->canEdit(auth()->user());
        $canApprove = $content->canApprove(auth()->user());

        // Get version history
        $versions = $content->versions()->with('creator')->latest()->get();

        return view('projects.content.show', compact(
            'project', 
            'content', 
            'userRole', 
            'canEdit', 
            'canApprove', 
            'versions'
        ));
    }

    /**
     * Show edit content form
     */
    public function edit(Project $project, ProjectContent $content)
    {
        if (!$content->canEdit(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk mengedit content ini.');
        }

        if ($content->project_id !== $project->id) {
            abort(404);
        }

        return view('projects.content.edit', compact('project', 'content'));
    }

    /**
     * Update content
     */
    public function update(Request $request, Project $project, ProjectContent $content)
    {
        if (!$content->canEdit(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk mengedit content ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'platform' => 'nullable|string',
            'metadata' => 'nullable|array',
            'change_notes' => 'nullable|string|max:500'
        ]);

        $content->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'platform' => $validated['platform'],
            'metadata' => $validated['metadata'] ?? [],
            'version' => $content->version + 1
        ]);

        // Create version history
        $content->createVersion('edited', $validated['change_notes'] ?? null);

        return redirect()->route('projects.content.show', [$project, $content])
            ->with('success', 'Content berhasil diupdate.');
    }

    /**
     * Submit content for review
     */
    public function submitForReview(Project $project, ProjectContent $content)
    {
        if (!$content->canEdit(auth()->user()) || !$content->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Content tidak dapat disubmit untuk review.'
            ], 403);
        }

        $content->submitForReview();

        // Notify approvers
        $this->notificationService->notifyContentSubmitted($content->load('project.user', 'project.members.user', 'creator'));

        return response()->json([
            'success' => true,
            'message' => 'Content berhasil disubmit untuk review.',
            'status' => $content->status
        ]);
    }

    /**
     * Approve content
     */
    public function approve(Request $request, Project $project, ProjectContent $content)
    {
        if (!$content->canApprove(auth()->user()) || !$content->isInReview()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk approve content ini.'
            ], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $content->approve(auth()->user(), $validated['notes'] ?? null);

        // Notify content creator
        $this->notificationService->notifyContentApproved($content->load('project', 'creator', 'reviewer'));

        return response()->json([
            'success' => true,
            'message' => 'Content berhasil di-approve.',
            'status' => $content->status
        ]);
    }

    /**
     * Reject content
     */
    public function reject(Request $request, Project $project, ProjectContent $content)
    {
        if (!$content->canApprove(auth()->user()) || !$content->isInReview()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk reject content ini.'
            ], 403);
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        $content->reject(auth()->user(), $validated['notes']);

        // Notify content creator
        $this->notificationService->notifyContentRejected($content->load('project', 'creator', 'reviewer'));

        return response()->json([
            'success' => true,
            'message' => 'Content berhasil di-reject.',
            'status' => $content->status
        ]);
    }

    /**
     * Add comment to content
     */
    public function addComment(Request $request, Project $project, ProjectContent $content)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'type' => 'required|in:comment,suggestion,approval,rejection',
            'highlighted_text' => 'nullable|array',
            'parent_id' => 'nullable|exists:content_comments,id'
        ]);

        $comment = $content->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
            'type' => $validated['type'],
            'highlighted_text' => $validated['highlighted_text'],
            'parent_id' => $validated['parent_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment berhasil ditambahkan.',
            'comment' => $comment->load('user')
        ]);
    }

    /**
     * Resolve comment
     */
    public function resolveComment(Project $project, ProjectContent $content, ContentComment $comment)
    {
        if (!$project->canUserEdit(auth()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk resolve comment.'
            ], 403);
        }

        $comment->resolve();

        return response()->json([
            'success' => true,
            'message' => 'Comment berhasil di-resolve.'
        ]);
    }

    /**
     * Show version history
     */
    public function versionHistory(Project $project, ProjectContent $content)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403);
        }

        $versions = $content->versions()->with('creator')->latest()->paginate(20);

        return view('projects.content.versions', compact('project', 'content', 'versions'));
    }

    /**
     * Restore version
     */
    public function restoreVersion(Project $project, ProjectContent $content, ContentVersion $version)
    {
        if (!$content->canEdit(auth()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk restore version.'
            ], 403);
        }

        $content->restoreVersion($version);

        return response()->json([
            'success' => true,
            'message' => "Content berhasil di-restore ke version {$version->version_number}.",
            'redirect' => route('projects.content.show', [$project, $content])
        ]);
    }

    /**
     * Delete content
     */
    public function destroy(Project $project, ProjectContent $content)
    {
        if (!$content->canEdit(auth()->user()) && !$project->isOwner(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk menghapus content ini.');
        }

        $content->delete();

        return redirect()->route('projects.content.index', $project)
            ->with('success', 'Content berhasil dihapus.');
    }

    /**
     * Generate AI content for collaboration projects
     */
    public function generateContent(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'tone' => 'required|string|in:professional,casual,friendly,exciting,persuasive',
            'type' => 'required|string|in:caption,article,ad_copy,email,product_desc',
            'platform' => 'nullable|string|in:instagram,facebook,tiktok,twitter,whatsapp,shopee,tokopedia,website',
            'local_language' => 'nullable|string',
            'business_context' => 'required|array',
            'business_context.name' => 'required|string',
            'business_context.type' => 'required|string',
            'business_context.description' => 'required|string',
            'business_context.audience' => 'required|string',
            'business_context.brand_tone' => 'required|string'
        ]);

        try {
            $aiService = app(\App\Services\AIService::class);
            
            // Build context-aware prompt
            $businessContext = $request->business_context;
            $prompt = $this->buildContentPrompt(
                $request->prompt,
                $request->type,
                $request->tone,
                $request->platform,
                $request->local_language,
                $businessContext
            );

            // Try AI generation with fallback
            try {
                $response = $aiService->generateText($prompt);
                
                if (!$response || empty($response)) {
                    throw new \Exception('Empty AI response');
                }
            } catch (\Exception $aiError) {
                // Fallback to template-based generation
                \Log::warning('AI Generation failed, using fallback: ' . $aiError->getMessage());
                $response = $this->generateFallbackContent($request, $businessContext);
            }

            return response()->json([
                'success' => true,
                'content' => $response,
                'metadata' => [
                    'type' => $request->type,
                    'platform' => $request->platform,
                    'tone' => $request->tone,
                    'character_count' => strlen($response)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Content Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again later.'
            ], 500);
        }
    }

    /**
     * Build AI prompt for content generation
     */
    private function buildContentPrompt($userPrompt, $type, $tone, $platform, $localLanguage, $businessContext)
    {
        $platformSpecs = [
            'instagram' => 'Instagram post (max 2200 characters, include relevant hashtags)',
            'facebook' => 'Facebook post (storytelling format, engaging)',
            'tiktok' => 'TikTok caption (max 150 characters, trendy and catchy)',
            'twitter' => 'Twitter/X post (max 280 characters, concise and impactful)',
            'whatsapp' => 'WhatsApp Status (short and punchy, max 100 characters)',
            'shopee' => 'Shopee product description (SEO optimized, persuasive)',
            'tokopedia' => 'Tokopedia product description (detailed, keyword-rich)',
            'website' => 'Website/blog content (comprehensive, SEO-friendly)'
        ];

        $typeInstructions = [
            'caption' => 'Create an engaging social media caption',
            'article' => 'Write a comprehensive article or blog post',
            'ad_copy' => 'Create persuasive advertisement copy',
            'email' => 'Write an effective email marketing message',
            'product_desc' => 'Create a compelling product description'
        ];

        $toneInstructions = [
            'professional' => 'Use professional, formal language',
            'casual' => 'Use casual, conversational tone',
            'friendly' => 'Use warm, friendly, and approachable tone',
            'exciting' => 'Use energetic, enthusiastic language',
            'persuasive' => 'Use compelling, action-oriented language'
        ];

        $prompt = "You are an expert copywriter for {$businessContext['name']}, a {$businessContext['type']} business.\n\n";
        $prompt .= "Business Description: {$businessContext['description']}\n";
        $prompt .= "Target Audience: {$businessContext['audience']}\n";
        $prompt .= "Brand Tone: {$businessContext['brand_tone']}\n\n";
        
        $prompt .= "Task: {$typeInstructions[$type]} based on this request: \"{$userPrompt}\"\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Tone: {$toneInstructions[$tone]}\n";
        
        if ($platform && isset($platformSpecs[$platform])) {
            $prompt .= "- Platform: {$platformSpecs[$platform]}\n";
        }
        
        // Add local language support
        if ($localLanguage) {
            $prompt .= "- Local Language: " . $this->getLocalLanguageInstruction($localLanguage) . "\n";
        }
        
        $prompt .= "- Stay consistent with the brand tone: {$businessContext['brand_tone']}\n";
        $prompt .= "- Make it relevant to the target audience: {$businessContext['audience']}\n";
        $prompt .= "- Focus on the business type: {$businessContext['type']}\n\n";
        
        if ($type === 'caption' && $platform) {
            $prompt .= "Include appropriate hashtags and emojis where suitable.\n";
        }
        
        $prompt .= "Generate only the content, no explanations or additional text.";

        return $prompt;
    }

    /**
     * Generate fallback content when AI service is unavailable
     */
    private function generateFallbackContent(Request $request, array $businessContext)
    {
        $templates = [
            'caption' => [
                'professional' => "🚀 {business_name} menghadirkan: {prompt}\n\nKami berkomitmen memberikan yang terbaik untuk {audience}. {business_type} kami menjamin kualitas dan keandalan.\n\n#Bisnis #Kualitas #Terpercaya",
                'casual' => "Halo semua! 👋 {business_name} di sini dengan sesuatu yang keren: {prompt}\n\nCocok banget buat {audience} yang suka {business_type} berkualitas!\n\n#KerenAbis #Komunitas #Suka",
                'friendly' => "Halo teman-teman! 😊 {business_name} senang berbagi: {prompt}\n\nKami peduli dengan {audience} dan ingin memberikan pengalaman {business_type} terbaik.\n\n#Ramah #Peduli #Komunitas",
                'exciting' => "🔥 KABAR GEMBIRA dari {business_name}! 🔥\n\n{prompt}\n\nIni SEMPURNA untuk {audience}! {business_type} kami semakin hari semakin baik!\n\n#Seru #Luar_Biasa #Sempurna",
                'persuasive' => "Jangan sampai terlewat! {business_name} menawarkan: {prompt}\n\nBergabunglah dengan ribuan {audience} yang sudah mempercayai {business_type} kami. Penawaran terbatas!\n\n#JanganSampaiTerlewat #Terpercaya #PenawaranTerbatas"
            ],
            'article' => [
                'professional' => "# {prompt}\n\nDi {business_name}, kami memahami pentingnya {business_type} berkualitas untuk {audience}.\n\n## Komitmen Kami\n\nKami berdedikasi memberikan layanan dan produk luar biasa yang memenuhi standar tertinggi.\n\n## Kesimpulan\n\nPilih {business_name} untuk solusi {business_type} yang andal dan profesional.",
                'casual' => "# {prompt}\n\nHai! Mari kita bicara tentang {business_name} dan mengapa kami cukup keren.\n\n## Apa yang Kami Lakukan\n\nKami fokus membuat {audience} senang dengan {business_type} kami yang luar biasa.\n\n## Mengapa Pilih Kami?\n\nKarena kami ramah, dapat diandalkan, dan benar-benar peduli dengan apa yang kami lakukan!",
            ],
            'ad_copy' => [
                'professional' => "{business_name}: {prompt}\n\nDipercaya oleh {audience} untuk {business_type} premium.\n\nHubungi kami hari ini untuk layanan profesional.",
                'persuasive' => "🎯 {prompt} - Hanya di {business_name}!\n\nBergabunglah dengan {audience} yang sudah mempercayai kami!\n\nBertindak sekarang - Penawaran terbatas!"
            ],
            'email' => [
                'professional' => "Subjek: {prompt}\n\nPelanggan yang Terhormat,\n\nTerima kasih telah memilih {business_name}. Kami menulis untuk menginformasikan tentang {prompt}.\n\nSebagai {business_type} terpercaya, kami berkomitmen melayani {audience} dengan keunggulan.\n\nSalam hormat,\nTim {business_name}",
                'friendly' => "Subjek: {prompt}\n\nHalo!\n\nSemoga kabar baik! Kami ingin berbagi berita menarik: {prompt}\n\nKami senang menjadi bagian dari komunitas {audience} dan menyediakan {business_type} yang hebat.\n\nSalam,\n{business_name}"
            ],
            'product_desc' => [
                'professional' => "{prompt}\n\nFitur Produk:\n- {business_type} berkualitas tinggi\n- Dirancang untuk {audience}\n- Didukung jaminan {business_name}\n\nSpesifikasi: Kelas profesional, andal, dan efisien.",
                'persuasive' => "{prompt}\n\n✅ Sempurna untuk {audience}\n✅ {business_type} premium\n✅ Dipercaya ribuan orang\n✅ Jaminan kualitas {business_name}\n\nPesan sekarang dan rasakan perbedaannya!"
            ]
        ];

        $type = $request->type;
        $tone = $request->tone;
        
        // Get template or use default
        $template = $templates[$type][$tone] ?? $templates[$type]['professional'] ?? "{business_type} terbaik dari {business_name}: {prompt}";
        
        // Replace placeholders
        $content = str_replace([
            '{business_name}',
            '{business_type}',
            '{audience}',
            '{prompt}'
        ], [
            $businessContext['name'],
            $businessContext['type'],
            $businessContext['audience'],
            $request->prompt
        ], $template);

        return $content;
    }

    /**
     * Get local language instruction for content generation
     */
    private function getLocalLanguageInstruction($language)
    {
        $instructions = [
            'jawa' => 'Include natural Javanese words like "Monggo", "Apik", "Murah pol", "Enak tenan", "Ojo nganti"',
            'sunda' => 'Include natural Sundanese words like "Mangga", "Saé", "Murah pisan", "Atuh", "Euy"',
            'betawi' => 'Include natural Betawi words like "Nih ye", "Kece badai", "Kagak", "Mantep jiwa"',
            'minang' => 'Include natural Minang words like "Lah", "Bana", "Rancak", "Lamak", "Uni/Uda"',
            'bali' => 'Include natural Balinese words like "Kenken", "Becik", "Niki", "Ajeg", "Suksma"',
            'batak' => 'Include natural Batak words like "Horas", "Lae", "Hatop", "Sai", "Tung mansai"',
            'madura' => 'Include natural Madurese words like "Kanca", "Bagus pisan", "Paran", "Enggi", "Salamat"',
            'bugis' => 'Include natural Bugis words like "Daeng", "Dekka", "Pole", "Siaga", "Makanja"',
            'banjar' => 'Include natural Banjar words like "Pian", "Banar", "Handak", "Kada", "Murah banar"',
            'mixed' => 'Mix 2-3 different regional languages naturally (e.g., combine Javanese + Sundanese + Betawi words)'
        ];

        return $instructions[$language] ?? "Include natural {$language} regional language elements";
    }
}