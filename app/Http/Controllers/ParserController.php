<?php

namespace App\Http\Controllers;

use App\Components\ParserClass;
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

    public function __construct(ParserService $parserService)
    {
        $this->parserService = $parserService;
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

    public function parse(Request $request)
    {


        $parsers = \App\Models\Parser::all();
        LogService::log('bold','запуск парсера');
        foreach ($parsers as $parser) {
            $parserClass = new ParserClass($parser);
            $parsed_data = $parserClass->parseData($parser);
            $new_companies = $parserClass->parseSinglePages($parser);
            $this->parserService->handleParsedData($new_companies,$parser->donor_id);
        }
        LogService::log('bold','работа парсера завершена');
        return 'ok';
    }

    public function logs()
    {
        $logs = ParserLog::orderBy('id','desc')->paginate();


        $statistics = [
            'parsed_companies_count'     => ParsedCompany::where('company_id', null)->count(),
            'new_parsed_companies_count' => ParsedCompany::where('company_id', null)->where('created_at', '=', Carbon::now()->subMinute(5))->count(),
            'reviews_count'              => Review::where('good', null)->count(),
            'new_reviews_count'          => Review::where('company_id', null)->where('created_at', '=', Carbon::now()->subMinute(5))->count(),
        ];


        return response()->json([
            'table'        => ''.view('admin.partials.logs', ['logs' => $logs,]),
            'statistics' => ''.view('admin.partials.parser.statistics', [
                'statistics' => $statistics,
            ]),
        ]);
    }
}
