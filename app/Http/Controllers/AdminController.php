<?php

namespace App\Http\Controllers;

use App\Exports\Export;
use App\ParserLog;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
    private $loaded;
    /**
     * @var Collection
     */
    private $proxies;

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

        for ($i = 0; $i < 1000; $i++) {
            $links[] = collect([
                'http://rater.club/'.$i,
            ])->random();
        }
        $this->proxies = collect([
            [
                'ip' => '127.0.0.1:8080',
                'requests' => 3,
            ],
            [
                'ip' => '77.92.223.100:57758',
                'requests' => 0,
            ],
            [
                'ip' => '50.224.173.190:8080',
                'requests' => 1,
            ],
            [
                'ip' => '89.17.42.37:42022',
                'requests' => 4,
            ],
            [
                'ip' => '124.41.211.139:39509',
                'requests' => 1,
            ],
            [
                'ip' => '91.90.232.101:57659',
                'requests' => 1,
            ],
            [
                'ip' => '46.255.81.82:55412',
                'requests' => 1,
            ],
            [
                'ip' => '194.67.167.234:39436',
                'requests' => 1,
            ],
            [
                'ip' => '93.170.82.242:46186',
                'requests' => 0,
            ],
            [
                'ip' => '187.64.111.129:43881',
                'requests' => 3,
            ],
        ]);
        $this->loaded = false;
        $requests     = function ($links) use ($client) {
            while(count($links) > 0){
                $link = array_pop($links);
                $proxies = $this->proxies;
                $proxy = $proxies->reduce(function($min,$current){
                    return $current['requests'] > $min['requests'] ? $current : $min;
                },$proxies[0]['requests']);
                yield function () use ($client, $link, &$links) {
                    return $client->getAsync($link,[
                        'proxy' => [
                            'http' => $proxy,
                            'https' => $proxy,
                        ]
                    ])->then(function () use ($link, &$links) {

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
            'concurrency' => 50,
        ]))->promise()->wait();

        return 'ok';
    }

}
