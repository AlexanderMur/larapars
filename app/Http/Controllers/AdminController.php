<?php

namespace App\Http\Controllers;

use App\Exports\Export;
use App\ParserLog;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;

class AdminController extends Controller
{
    private $loaded;

    public function allData()
    {

    }


    public function export()
    {
        return \Excel::download(new Export, 'model.xls');
    }

    public function logs()
    {
        return view('admin.logs.index', [
            'logs' => ParserLog::orderBy('id', 'desc')->paginate(),
        ]);
    }

    public function memoryTest()
    {

        $client = new Client(['proxy' => ['http' => '127.0.0.1:8080'], 'timeout' => 2]);


        $links = [];

        for ($i = 0; $i < 100; $i++) {
            $links[] = collect([
                'http://rater.club/'.$i,
            ])->random();
        }

        $this->loaded = false;
        $requests     = function ($links) use ($client) {
            while(count($links) > 0){
                $link = array_pop($links);
                info(implode(', ',$links));
                yield function () use ($client, $link, &$links) {
                    return $client->getAsync($link)->then(function () use ($link, &$links) {

                    }, function () use (&$links, $link) {
                        if(!$this->loaded){
                            $links[] = $link.'bad';
                            $this->loaded = true;
                        }
                    });
                };
            }
        };

        (new Pool($client, $requests($links), [
            'concurrency' => 500,
        ]))->promise()->wait();

        return 'ok';
    }

}
