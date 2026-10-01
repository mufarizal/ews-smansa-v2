<?php

namespace App\Console\Commands;

use App\Services\AIService;
use Illuminate\Console\Command;

class ProcessAiFeedback extends Command
{
    protected $signature = 'ai:process-feedback';

    protected $description = 'Proses semua feedback guru yang masih berstatus menunggu, regenerasi rekomendasi terkait';

    public function handle(AIService $aiService): int
    {
        $this->info('Memproses feedback yang menunggu...');
        $aiService->regeneratePendingFeedback();
        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
