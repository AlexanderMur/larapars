<?php

namespace App\Http\Controllers;

use App\Components\ParserClass;
use App\Http\Requests\StartParserRequest;
use App\Jobs\ParsePages;
use App\Models\Donor;
use App\Models\HttpLog;
use App\Models\ParserTask;
use App\ParserLog;
use App\Services\ParserService;

class ParserController extends Controller
{
    /**
     * @var ParserService
     */
    public $parserService;
    public $parserClass;

    public function __construct(ParserService $parserService)
    {
        $this->parserService = $parserService;
        $this->parserClass   = new ParserClass();
    }

    public function index()
    {

    }

    public function start()
    {
        $logs = ParserLog::paginate();
        return view('admin.parser', [
            'logs' => $logs,
        ]);
    }

    public function manualParser()
    {
        if (\request()->isMethod('POST')) {
            request()->flash();
            $urls = preg_split('/\r\n/', request('page'));
            $this->parserService->parse($urls, 'companies');
        }
        $donors = Donor::all();
        return view('admin.parser.manual', [
            'donors' => $donors,
        ]);
    }

    public function parse(StartParserRequest $request)
    {
        if ($request->stop) {
            $this->parserService->stop();
            return 'ok';
        }
        if ($request->resume) {
            $this->parserService->resume();
            return 'ok';
        }
        if ($request->donor_id) {
            if ($request->donor_id === 'all') {
                $links = Donor::massParsing()->get()->pluck('link');
            } else {
                $links = [Donor::find($request->donor_id)->link];
            }
            dispatch(new ParsePages($links, 'archivePages'));
        }
        if ($request->pages) {
            dispatch(new ParsePages($request->pages, 'companies'));
        }

        return 'ok';
    }

    public function logs()
    {
        $task = ParserTask::latest('id')->withStats()->first();
        $logs = ParserLog::latest('id')->paginate();
        $http_logs = HttpLog::latest('id')->paginate();

        $statistics = $this->parserService->getStatistics();
        return response()->json([
                'messages'      => '' . view('admin.parser.__messages', ['logs' => $logs]),
                'http'      => '' . view('admin.parser.__http_table', ['http_logs'=>$http_logs]),
                'statistics' => '' . view('admin.partials.parser.statistics', [
                        'statistics' => $statistics,
                        'task'       => $task,
                    ]),
            ] + $statistics);
    }
}
