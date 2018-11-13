<?php

namespace App\Jobs;

use App\Models\HttpLog;
use App\Models\ParserTask;
use App\Services\ParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResumeParsePages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var ParserService
     */
    public $parserService;
    /**
     * @var array
     */
    protected $links;
    protected $type;
    protected $timeout = 2;
    /**
     * @var ParserTask $task
     */
    protected $task;
    /**
     * @var ParserTask $old_task
     */
    protected $old_task;
    protected $task_id;
    protected $old_task_id;


    /**
     * Create a new job instance.
     *
     * @param $task_id
     * @param $old_task_id
     */
    public function __construct($task_id, $old_task_id)
    {
        $this->task_id = $task_id;
        $this->old_task_id = $old_task_id;
    }

    /**
     * Execute the job.
     *
     * @param ParserService $parserService
     * @return void
     */
    public function handle(ParserService $parserService)
    {

        $this->task = ParserTask::find($this->task_id);
        $this->old_task = ParserTask::find($this->old_task_id);
        $parserService->create_task($this->task);
        /**
         * @var HttpLog[] $not_loaded_urls
         */
        $not_loaded_urls = $this->old_task->http_logs()
            ->with('donor')->where('status', null)->get();

        $visited_urls                = $this->old_task->http_logs()
            ->where('status', '!=', null)
            ->get()->map->url->toArray();
        $parserService->visitedPages = $visited_urls;
        $this->task->setParsing();
        foreach ($not_loaded_urls as $not_loaded_url) {
            if ($not_loaded_url->channel == 'company') {
                $promise = $parserService->parseCompanyByUrl($not_loaded_url->url, $not_loaded_url->donor);

                if ($this->task->type == 'company') {
                    $promise->then([$this->task, 'tickProgress'], [$this->task, 'tickProgress']);
                }
            }
            if ($not_loaded_url->channel == 'archive') {
                $parserService->parseArchivePageByUrl($not_loaded_url->url, $not_loaded_url->donor)
                    ->then([$this->task, 'tickProgress'], [$this->task, 'tickProgress']);
            }
        }
        $parserService->run();
        $parserService->log_end();

    }
    public function failed()
    {

        $this->task->setDone();
    }
}
