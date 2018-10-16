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
    public $links;

    /**
     * Create a new job instance.
     *
     * @param array $links
     */
    public function __construct($links = [])
    {
        $this->links = $links;
    }

    /**
     * Execute the job.
     *
     * @param ParserService $parserService
     * @return void
     */
    public function handle(ParserService $parserService)
    {
        $this->parserService = $parserService;

        $this->parserService->parseArchivePagesByUrls($this->links);
    }
}
