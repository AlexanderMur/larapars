<?php

namespace App\Jobs;

use App\Components\ParserClient;
use App\Models\Donor;
use App\Models\ParserTask;
use App\Parsers\Parser;
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
    public $canceled;


    /**
     * Create a new job instance.
     *
     * @param $task_id
     * @param array $links
     * @param bool $recursive
     */
    public function __construct($task_id, $links = [], $recursive = true)
    {
        $this->links     = $links;
        $this->task_id   = $task_id;
        $this->recursive = $recursive;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        ini_set('memory_limit','512M');
        ini_set('max_execution_time',0);
        $this->task = ParserTask::find($this->task_id);
        info('parser_start');
        $this->task->log('bold', 'Запуск парсера', null);

        $urls    = Donor::mapUrls($this->links);
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

        $this->task->setParsing();
        $this->task->setProgressNow();
        $this->task->setProgressMax(count($urls));

        if ($this->task->type == 'companies') {
            foreach (array_reverse($urls) as $url) {
                /**
                 * @var Parser $parser
                 */
                $parser = $url['donor']->getParser($client, $this->task, $proxies, $tries);
                $parser->parseCompanyByUrl($url['donor_page'])
                    ->then([$this->task, 'tickProgress'], [$this->task, 'tickProgress']);
            }
            $client->run();
            $this->handle_end();
        }

        if ($this->task->type == 'archivePages') {
            foreach (array_reverse($urls) as $url) {
                /**
                 * @var Parser $parser
                 */
                $parser = $url['donor']->getParser($client, $this->task, $proxies, $tries);
                $parser->parseAll()
                    ->then(function () {
                        $this->task->tickProgress();
                    });
            }
            $client->run();
            $this->handle_end();
        }

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
