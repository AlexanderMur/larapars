<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    private $callback;

    /**
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data = 'de',$b = 'de')
    {
        $this->callback = $b;
        $this->data = $data;
        info('construcTt!');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(2);
        info('again 2 secs...');
        sleep(2);
        info('Hi!!!');

    }
    public function __destruct()
    {
        info('desctruct');
    }
}
