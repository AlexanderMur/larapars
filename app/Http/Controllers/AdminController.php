<?php

namespace App\Http\Controllers;

use App\Exports\ModelExport;
use App\Models\Company;
use App\ParserLog;

class AdminController extends Controller
{
    public function allData()
    {

        $companies = Company::with(['donors', 'reviews'])->get();
        return view('admin.companies.index', [
            'companies' => $companies,
        ]);
    }


    public function export()
    {
        return \Excel::download(new ModelExport, 'model.xls');
    }

    public function logs()
    {
        return view('admin.logs.index', [
            'logs' => ParserLog::orderBy('id', 'desc')->paginate(),
        ]);
    }
}
