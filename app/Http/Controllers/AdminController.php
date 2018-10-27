<?php

namespace App\Http\Controllers;

use App\Components\ParserClient;
use App\Exports\Export;
use App\ParserLog;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
    private $loaded;
    /**
     * @var Collection
     */
    private $proxies;
    public $links = [];
    /**
     * @var Client $client
     */
    private $client;
    private $yilded;
    private $num = 0;
    private $counter = 0;

    public function __construct()
    {
        $this->client = new ParserClient();
    }

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



        return 'ok';
    }
    public function addGet($link){
        return $this->client->addGet($link,[
            'proxy' => [
                'http' => '127.0.0.1:8080',
            ]
        ]);
    }
    public function test2()
    {
        $this->counter = 0;
        $this->addGet('http://jsonplaceholder.typicode.com/todos/0')
            ->then(function () {
                $this->counter++;
                info('resolve!!!!!!!!!11');
                echo 'AAA';
                return 'ok!!!!';
            });

        $this->client->run();

        echo $this->counter;
        return 'ok';
    }
}
