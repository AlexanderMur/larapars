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
        foreach ($items as $donor) {

            foreach ($donor as $item) {
                /** @var Donor $donor */
                $donor = $item['parser']->donor;

                $company = new Company();
                $company->title = $item['title'];
                $company->address = $item['address'];
                $company->single_page_link = $item['single_page_link'];
                /** @var Review[] $reviews */

                $donor->companies()->save($company);

                foreach ($item['reviews'] as $review) {

                    $review_model = new Review();
                    $review_model->text = $review['text'];
                    $review_model->title = $review['title'];
                    $review_model->name = $review['name'];

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
