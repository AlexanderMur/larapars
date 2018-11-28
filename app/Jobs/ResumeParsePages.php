<?php

namespace App\Jobs;

use App\Components\ParserClient;
use App\Models\HttpLog;
use App\Models\ParserTask;
use App\Parsers\Parser;
use App\Services\ParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

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
    public $canceled;


    /**
     * Create a new job instance.
     *
     * @param $task_id
     * @param $old_task_id
     */
    public function __construct($task_id, $old_task_id)
    {
        $this->task_id     = $task_id;
        $this->old_task_id = $old_task_id;
    }


    public function handle()
    {
        $this->task     = ParserTask::find($this->task_id);
        $this->old_task = ParserTask::find($this->old_task_id);


        info('parser_start');
        $this->task->log('bold', 'Запуск парсера', null);


        $this->task->setProgressNow($this->old_task->progress_now);
        $this->task->setParsing();
        $client  = new ParserClient();
        $proxies = setting()->getProxies();
        $tries   = setting()->tries ?? 2;
        $client->onConcurrency(function () {
            static $lastUpdate;
            static $concurrency;

            if (microtime(true) - $lastUpdate > 2) {
                $lastUpdate  = microtime(true);
                $concurrency = setting()->concurrency;
            };

            return $concurrency;
        });


        /**
         * @var Collection|HttpLog[] $not_loaded_urls
         */
        $not_loaded_urls = $this->old_task->http_logs()
            ->with('donor')->where('status', null)->get();


//        $groups = $not_loaded_urls->groupBy->donor_id;

        $visitedPages = $this->old_task->http_logs()
            ->with('donor')->where('status', '!=', null)
            ->get()->toArray();

        foreach ($not_loaded_urls as $not_loaded_url) {
            if ($not_loaded_url->channel == 'parseCompanyByUrl') {
                /**
                 * @var Parser $parser
                 */
                $parser  = $not_loaded_url->donor->getParser($client, $this->task, $proxies, $tries);
                $promise = $parser->parseCompanyByUrl($not_loaded_url->url);

                if ($this->task->type == 'company') {
                    $promise->then([$this->task, 'tickProgress'], [$this->task, 'tickProgress']);
                }
            }
        }
        $client->run();
        foreach ($not_loaded_urls as $not_loaded_url) {
            if ($not_loaded_url->channel == 'parserAll') {
                /**
                 * @var Parser $parser
                 */
                $parser = $not_loaded_url->donor->getParser($client, $this->task, $proxies, $tries);


                $parser->visitedPages = $visitedPages;
                $parser->parseAll($not_loaded_url->url)
                    ->then(function () {
                        $this->task->tickProgress();
                    });
            }
        }
        $client->run();
        $this->handle_end();
    }

    public function handle_end()
    {
        info('ENDPARSING');

        $this->task = $this->task->getFresh();

        $this->task->refreshState();

        $this->task->log('bold', '
            Работа парсера ' . ($this->task->isPausingOrPaused() ? 'приостановлена' : 'завершена') . '. Найдено новых компаний: (' . $this->task->new_companies_count . ')
            Обновлено компаний: (' . $this->task->updated_companies_count . ')
            Новых отзывов: (' . $this->task->new_reviews_count . ')
            Удалено отзывов: (' . $this->task->deleted_reviews_count . ')
            Возвращено отзывов: (' . $this->task->restored_reviews_count . ')
            ', null);
        if ($this->task->isPausingOrPaused()) {
            $this->task->setPaused();
        } else {
            $this->task->setDone();
        }
    }

    public function failed()
    {

        $this->task->setDone();
    }
}
