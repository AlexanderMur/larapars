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
            $this->parserService->create_task();
            $this->parserService->parse($urls, 'companies');
            $this->parserService->log_end();
        }
        $donors = Donor::all();
        return view('admin.parser.manual', [
            'donors' => $donors,
        ]);
    }

    public function parse(StartParserRequest $request)
    {
        if ($request->donor_id) {
            if ($request->donor_id === 'all') {
                $links = Donor::massParsing()->get()->pluck('link');
            } else {
                $links = [Donor::find($request->donor_id)->link];
            }
            $task = ParserTask::dispatch($links, 'archivePages');
            return response()->json(['id' => $task->id]);
        }
        if ($request->pages) {
            $task = ParserTask::dispatch($request->pages, 'companies');
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
            $http_logs = HttpLog::latest('id')->paginate(30);
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
