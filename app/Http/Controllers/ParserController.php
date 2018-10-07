<?php

namespace App\Http\Controllers;

use App\Components\ParserClass;
use App\Models\Donor;
use App\Models\ParsedCompany;
use App\Models\Review;
use App\ParserLog;
use App\Services\LogService;
use App\Services\ParserService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $logs = ParserLog::query()->paginate();
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

    public function parse(Request $request)
    {


        LogService::log('bold', 'запуск парсера');

        $parsers = \App\Models\Parser::all();
        $links = [];
        foreach ($parsers as $parser) {
            $links[] = $parser->donor->link;
        }
        $this->parserService->parseArchivePagesByUrls($links);

        LogService::log('bold', 'работа парсера завершена');
        return 'ok';
    }

    public function logs()
    {
        $logs = ParserLog::orderBy('id', 'desc')->paginate();


        $statistics = [
            'parsed_companies_count'     => ParsedCompany::where('company_id', null)->count(),
            'new_parsed_companies_count' => ParsedCompany::where('company_id', null)->where('created_at', '=', Carbon::now()->subMinute(5))->count(),
            'reviews_count'              => Review::where('good', null)->count(),
            'new_reviews_count'          => Review::where('company_id', null)->where('created_at', '=', Carbon::now()->subMinute(5))->count(),
        ];


        return response()->json([
            'table'      => '' . view('admin.partials.logs', ['logs' => $logs,]),
            'statistics' => '' . view('admin.partials.parser.statistics', [
                    'statistics' => $statistics,
                ]),
        ]);
    }
}
