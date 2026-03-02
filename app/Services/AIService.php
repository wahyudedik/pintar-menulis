<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;

class AIService
{
    /**
     * Menghasilkan copywriting menggunakan Google Gemini API.
     *
     * @param array $params Parameter untuk pembuatan konten (type, brief, tone, platform, keywords)
     * @return string Konten copywriting yang dihasilkan
     * @throws \Exception Jika gagal menghasilkan konten
     */
    public function generateCopywriting(array $params)
    {
        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($params);

        try {
            // Menggunakan Gemini 1.5 Flash yang efisien dan cepat
            $result = Gemini::generativeModel('gemini-1.5-flash')
                ->generateContent($systemPrompt . "\n\n" . $userPrompt);

            return $result->text();
        } catch (\Exception $e) {
            throw new \Exception('Gagal menghasilkan copywriting: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan System Prompt yang mendefinisikan persona AI.
     */
    protected function getSystemPrompt(): string
    {
        return <<<EOT
        Kamu adalah "Smart Copy SMK", asisten copywriter AI profesional yang khusus membantu UMKM Indonesia.
        Tugasmu adalah membuat konten promosi yang persuasif, menarik, dan sesuai dengan kaidah Bahasa Indonesia yang baik namun tetap relevan dengan tren pasar.
        
        Karakteristik tulisanmu:
        1. Memahami psikologi konsumen Indonesia (suka promo, testimoni, dan keakraban).
        2. Mampu menyesuaikan gaya bahasa (tone) dari formal hingga sangat santai/gaul.
        3. Selalu menyertakan Call to Action (CTA) yang kuat dan jelas.
        4. Menggunakan emoji secara bijak untuk meningkatkan engagement (terutama untuk media sosial).
        5. Hasil tulisan harus rapi, mudah dibaca, dan bebas dari kesalahan ketik.
        EOT;
    }

    /**
     * Membangun prompt spesifik berdasarkan parameter dari user.
     */
    protected function buildPrompt(array $params): string
    {
        $type = $params['type'] ?? 'caption';
        $brief = $params['brief'] ?? '';
        $tone = $this->getToneDescription($params['tone'] ?? 'casual');
        $platform = $params['platform'] ?? 'instagram';
        $keywords = $params['keywords'] ?? '';

        $prompt = "### INSTRUKSI KERJA\n";
        $prompt .= "Buatkan **" . strtoupper(str_replace('_', ' ', $type)) . "** untuk platform **" . strtoupper($platform) . "**.\n";
        $prompt .= "Gaya Bahasa (Tone): {$tone}\n\n";

        $prompt .= "### BRIEF PRODUK/JASA\n";
        $prompt .= "{$brief}\n\n";

        if ($keywords) {
            $prompt .= "### KATA KUNCI WAJIB\n";
            $prompt .= "Masukkan kata-kata ini secara natural: {$keywords}\n\n";
        }

        $prompt .= "### FORMAT & ATURAN OUTPUT\n";

        // Aturan spesifik berdasarkan tipe konten
        switch ($type) {
            case 'caption':
                $prompt .= "1. Mulai dengan 'Hook' yang memancing rasa penasaran atau relate dengan masalah audiens.\n";
                $prompt .= "2. Isi dengan manfaat produk (benefit), bukan sekadar fitur.\n";
                $prompt .= "3. Akhiri dengan CTA yang jelas (misal: 'Klik link di bio', 'DM untuk tanya harga').\n";
                $prompt .= "4. Tambahkan 5-10 hashtag relevan di bagian paling bawah.\n";
                break;
            case 'product_description':
                $prompt .= "1. Gunakan struktur: Judul Produk -> Deskripsi Menarik -> Keunggulan/Fitur Utama -> Cara Order.\n";
                $prompt .= "2. Buat poin-poin (bullet points) agar mudah dibaca cepat (scanning).\n";
                $prompt .= "3. Tekankan pada 'Kenapa harus beli sekarang?'.\n";
                break;
            case 'headline':
                $prompt .= "1. Berikan 5 variasi headline yang berbeda (misal: gaya berita, gaya tanya, gaya perintah).\n";
                $prompt .= "2. Fokus pada hasil instan atau solusi masalah.\n";
                break;
            case 'cta':
                $prompt .= "1. Berikan 10 variasi kalimat Call to Action singkat dan powerfull.\n";
                break;
        }

        $prompt .= "\nBahasa: Indonesia\n";
        $prompt .= "Target Audience: UMKM dan Pelanggan Indonesia\n";

        return $prompt;
    }

    /**
     * Mendapatkan deskripsi detail untuk setiap tone bahasa.
     */
    protected function getToneDescription(string $tone): string
    {
        $tones = [
            'casual' => 'Santai, akrab, seperti berbicara dengan teman, menggunakan kata "kamu" atau "gaes".',
            'formal' => 'Profesional, sopan, berwibawa, menggunakan kata "Anda", cocok untuk B2B atau produk mewah.',
            'persuasive' => 'Sangat mengajak, penuh energi, menekankan pada urgensi dan keuntungan (hard selling).',
            'funny' => 'Menghibur, menggunakan sedikit humor atau receh yang relate, cocok untuk engagement tinggi.',
            'emotional' => 'Menyentuh hati, menggunakan teknik storytelling, fokus pada perasaan dan empati.',
            'educational' => 'Informatif, jelas, menjelaskan "how-to" atau manfaat secara logis.',
        ];

        return $tones[$tone] ?? $tones['casual'];
    }
}
