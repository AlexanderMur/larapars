<?php

namespace App\Http\Controllers;

use App\Components\Parser;
use App\Components\Parser2;
use App\Components\ParserClass;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;

class ParserController extends Controller
{
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

        return view('admin.parser',[
            'tables' => $items,
        ]);
    }
}
