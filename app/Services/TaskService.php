<?php

namespace App\Services;

use App\Jobs\ProcessCheckingProxies;
use App\Models\Host;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService extends BaseService
{
    /**
     * Create task from request array
     *
     * @param array $hosts
     *
     * @return Task|null
     *
     */
    public function saveTaskWithHosts(array $inputHosts) : ?Task
    {
        $newTask = Task::create();
        $taskId = $newTask->id;

        $hosts = collect($inputHosts)->each(fn ($item) => Host::create(['address' => $item, 'task_id' => $taskId]));
        //dd($hosts, $newTask->hosts);

        return $newTask;
    }

    /**
     * Process checking proxy for every host for this task
     * Chunk hosts and dispatch each of them
     *
     * @param Task $task
     * @param mixed $chunkNumber=10
     *
     * @return void
     *
     */
    public function startCheckingHosts(Task $task, $chunkNumber=10): void
    {
        $task->hosts()->chunk($chunkNumber, function (Collection $hosts) {
            $this->startCheckingJob($hosts);
        });
    }
    
    /**
     * Make statistic for front
     *
     * @param Task $task
     *
     * @return int
     *
     */
    public function getNumberOfCompleted(Task $task): int
    {
        return $task->hosts()->completed()->count();
    }

    /**
     * Run new job for each hosts chunk
     *
     * @param Collection $hosts
     *
     * @return void
     *
     */
    private function startCheckingJob(Collection $hosts): void
    {
        ProcessCheckingProxies::dispatch($hosts);
    }


}
