<?php

namespace App\Jobs;

use App\Models\ParserTask;
use App\Services\ParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParsePages implements ShouldQueue
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
    protected $timeout = 2;
    /**
     * @var ParserTask $task
     */
    protected $task;
    protected $task_id;
    protected $recursive;


    /**
     * Create a new job instance.
     *
     * @param $task_id
     * @param array $links
     * @param bool $recursive
     */
    public function __construct($task_id, $links = [],$recursive = true)
    {
        $this->links   = $links;
        $this->task_id = $task_id;
        $this->recursive = $recursive;
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
        $urls       = $parserService->mapUrlsWithDonor($this->links);
        $parserService->create_task($this->task);
        $this->task->setParsing();
        $this->task->setProgressNow();
        if ($this->task->type == 'companies') {
            foreach ($urls as $url) {
                $parserService->parseCompanyByUrl($url['donor_page'], $url['donor'])
                    ->then([$this->task, 'tickProgress'], [$this->task, 'tickProgress']);
            }
        }
        if ($this->task->type == 'archivePages') {
            foreach ($urls as $url) {
                $parserService->parseArchivePageByUrl($url['donor_page'], $url['donor'],$this->recursive)
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
