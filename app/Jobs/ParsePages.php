<?php

namespace App\Jobs;

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
    protected $type;

    /**
     * Create a new job instance.
     *
     * @param array $links
     * @param $type
     */
    public function __construct($links = [],$type)
    {
        $this->links = $links;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @param ParserService $parserService
     * @return void
     */
    public function handle(ParserService $parserService)
    {

        $parserService->parse($this->links,$this->type);
    }


    public function start(){
        if (!$this->is_started) {
            info('start');
            $this->start       = microtime(true);
            $this->proxies     = setting()->getProxies();
            $this->parser_task = ParserTask::create();
            $this->parser_task->log('bold', 'Запуск парсера', null);
            $this->state = 'parsing';
            $this->saveProgress();
            if (file_exists($this->stop_file_path())) {
                unlink($this->stop_file_path());
            }
            config()->set('debugbar.collectors.db', false);
            config()->set('debugbar.collectors.log', false);
            config()->set('debugbar.collectors.logs', false);
            config()->set('debugbar.enabled', false);
            $this->is_started = true;
        }
        if (file_exists($this->stop_file_path())) {
            $this->state = 'stopping';
        }
    }
    public function captureEnd(){
        info('ENDPARSING');
        $this->parser_task->log('bold', '
            Работа парсера завершена. Найдено новых компаний: (' . $this->new_parsed_companies_count . ')
            Обновлено компаний: (' . $this->updated_companies_count . ')
            Новых отзывов: (' . $this->new_reviews_count . ')
            Удалено отзывов: (' . $this->deleted_reviews_count . ')
            Возвращено отзывов: (' . $this->restored_reviews_count . ')
            ', null,['donor_stats' => $this->donors]);
        if ($this->should_stop()) {
            $this->state = 'paused';
        } else {
            $this->state = 'done';
        }
        if (file_exists($this->stop_file_path())) {
            unlink($this->stop_file_path());
        }
        $this->saveProgress();
    }
}
