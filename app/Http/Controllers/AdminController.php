<?php

namespace App\Http\Controllers;

use App\Components\ParserClient;
use App\Exports\Export;
use App\ParserLog;
use GuzzleHttp\Client;

class AdminController extends Controller
{
    public $links = [];
    /**
     * @var Client $client
     */
    private $client;
    private $start;

    public function __construct()
    {
        $this->client = new ParserClient();
        $this->start  = microtime(true);
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


}
