<?php

namespace App\Http\Controllers;

use App\Components\ParserClass;
use App\Http\Requests\StartParserRequest;
use App\Models\Company;
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
            ParserTask::dispatch_now($urls,'companies');
        }
        $donors = Donor::all();
        return view('admin.parser.manual', [
            'donors' => $donors,
        ]);
    }

    public function parse(StartParserRequest $request)
    {
        if ($request->donor_ids) {
            $links = Donor::findMany($request->donor_ids)->map(function (Donor $donor){return $donor->link;});

            $task = ParserTask::dispatch_now($links, 'archivePages');
            return response()->json(['id' => $task->id]);
        }
        if ($request->pages) {
            $task = ParserTask::dispatch_now($request->pages, 'companies');
            return response()->json(['id' => $task->id]);
        }

        return 'ok';
    }

    public function logs()
    {

        $task     = ParserTask::latest('id')->withStats()->first();
        $task_arr = $task !== null ? $task->toArray() : [];


        $statistics = $this->parserService->getStatistics();
        if (!request('company_id')) {
            $logs      = ParserLog::latest('id')->paginate(30);
            $http_logs = HttpLog::latest('sent_at')->latest('id')->where('sent_at','!=',null)->paginate(30);
            return response()->json([
                    'messages'   => '' . view('admin.parser.__messages', ['logs' => $logs]),
                    'http'       => '' . view('admin.parser.__http_table', ['http_logs' => $http_logs]),
                    'statistics' => '' . view('admin.partials.parser.statistics', [
                            'statistics' => $statistics,
                            'task'       => $task,
                        ]),
                ] + $task_arr + $statistics);
        } else {
            $company = Company::find(request('company_id'));
            $tasks   = $company->getTasks();
            return response()->json([
                    'messages' => '' . view('admin.parser.__messages', ['logs' => $tasks->flatMap->logs]),
                    'http'     => '' . view('admin.parser.__http_table', ['http_logs' => $tasks->flatMap->http_logs]),
                ] + $task_arr + $statistics);
        }


    }
}
