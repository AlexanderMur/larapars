<?php

namespace App\Http\Controllers;

use App\Components\ParserClass;
use App\Models\ParsedCompany;
use App\Models\Review;
use App\ParserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParserController extends Controller
{
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
        foreach ($parsers as $parser) {
            $parserClass = new ParserClass($parser);
            $parsed_data = $parserClass->parseData($parser);
            $new_companies = $parserClass->parseSinglePages($parser);


            foreach ($new_companies as $new_company) {
                $parsed_company = ParsedCompany::updateOrCreate(['donor_page'=>$new_company['single_page_link']],$new_company);
                foreach ($new_company['reviews'] as $new_review) {
                    $parsed_company->reviews()->updateOrCreate(
                        ['text'=>$new_review['text']],
                        array_merge(
                            $new_review,
                            [
                                'donor_id'=>$parser->donor_id,
                                'donor_link'=>$new_company['single_page_link']
                            ]
                        )
                    );
                }
            }

        }
        return 'ok';
    }

    public function logs()
    {
        $logs = ParserLog::latest()->paginate();


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
