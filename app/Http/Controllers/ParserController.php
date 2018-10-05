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
            $company = $this->parserClass
                ->parseCompany(\request('page'), Donor::find(\request('donor_id')))
                ->wait();
            dump($company);
            $this->parserService->handleParsedData([$company]);
        }
        $donors = Donor::all();
        return view('admin.parser.manual', [
            'donors' => $donors,
        ]);
    }

    public function parse(Request $request)
    {


        $parsers = \App\Models\Parser::all();
        LogService::log('bold', 'запуск парсера');
        $parserClass = $this->parserClass;
        foreach ($parsers as $parser) {
            $link      = $parser->donor->link;
            $companies = $parserClass->parseData($link, $parser->donor)->wait();
            LogService::log('info', 'спарсено ' . count($companies) . ' компаний', $link);
            foreach ($companies as $key => $company) {

                $companies[$key] = array_merge(
                    $companies[$key],
                    $parserClass->parseCompany($company['donor_page'], $parser->donor)->wait()
                );
                LogService::log('info', 'спарсено ' . count($companies[$key]['reviews']) . ' отзывов', $companies[$key]['donor_page']);
            }
            $this->parserService->handleParsedData($companies);
        }
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
