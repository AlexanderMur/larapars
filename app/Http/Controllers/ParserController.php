<?php

namespace App\Http\Controllers;

use App\Components\ParserClass;
use App\Http\Requests\StartParserRequest;
use App\Models\Donor;
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
        $parsers = \App\Models\Parser::all();
        return view('admin.companies.index', [
            'companies' => $parsers,
        ]);
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

            $this->parserService->parseCompaniesByUrls(
                preg_split('/\r\n/', request('page'))
            );

        }
        $donors = Donor::all();
        return view('admin.parser.manual', [
            'donors' => $donors,
        ]);
    }

    public function parse(StartParserRequest $request)
    {
        if ($request->donor_id === 'all') {
            $links = Donor::all()->pluck('link');
        } else {
            $links = [Donor::find($request->donor_id)->link];
        }
        $this->parserService->parseArchivePagesByUrls($links);

        return 'ok';
    }

    public function logs()
    {
        $logs = ParserLog::orderBy('id', 'desc')->paginate();
        return response()->json([
            'table'      => '' . view('admin.partials.logs', ['logs' => $logs,]),
            'statistics' => '' . view('admin.partials.parser.statistics', [
                    'statistics' => $this->parserService->getStatistics(),
                ]),
        ]);
    }
}
