<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\CheckingProxyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCheckingProxies implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Collection $hosts)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(CheckingProxyService $checkingService): void
    {
        $checkingService->processChunk($this->hosts);
    }
}
