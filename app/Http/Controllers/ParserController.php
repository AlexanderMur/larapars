<?php

namespace App\Http\Controllers;

use App\Components\Parser;
use App\Components\Parser2;
use App\Components\ParserClass;
use App\Models\Company;
use App\Models\Donor;
use App\Models\Review;
use DebugBar;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;

class ParserController extends Controller
{
    public function index(){
        $parsers = \App\Models\Parser::all();
        return view('admin.companies.index',[
            'companies' => $parsers,
        ]);
    }
    public function start(){
        return view('admin.parser');
    }
    public function parse(Request $request){

        $parserClass = new ParserClass();

        $parsers = \App\Models\Parser::all();
        $items = [];
        $how_many = $request->get('how_many');;
        foreach ($parsers as $parser) {
            $parsed_data = $parserClass->parseData($parser,$how_many);
            $items[] = $parserClass->parseSinglePages($parser);
        }
        foreach ($items as $companies) {

            foreach ($companies as $parsed_company) {
                /** @var Donor $donor */
                $donor = $parsed_company['parser']->donor;

                $company = new Company($parsed_company);
                $company->phone = implode(', ',$parsed_company['phones']);

                $donor->companies()->save($company);

                foreach ($parsed_company['reviews'] as $review) {

                    $review_model = new Review($review);
                    $review_model->donor()->associate($donor);
                    $review_model->company()->associate($company);
                    $review_model->save();
                }
            }

        }
        return view('admin.parser',[
            'tables' => $items,
        ]);
    }
}
