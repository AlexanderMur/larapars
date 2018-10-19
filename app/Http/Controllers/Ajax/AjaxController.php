<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 19.10.2018
 * Time: 19:31
 */

namespace App\Http\Controllers\Ajax;


use App\Http\Controllers\Controller;
use App\Models\ParsedCompany;

class AjaxController extends Controller
{
    /**
     * @param ParsedCompany $parsedCompany
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getParsedCompanyHistory(ParsedCompany $parsedCompany)
    {
        return response()->json(
            view('admin.parsed_companies.partials._history', [
                'parsed_company' => $parsedCompany,
            ])->render()
        );
    }
}