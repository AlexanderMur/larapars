<?php

namespace App\Http\Controllers;

use App\Exports\Export;
use App\ParserLog;

class AdminController extends Controller
{
    public function allData()
    {

    }


    public function export()
    {
        return \Excel::download(new Export, 'model.xls');
    }

    public function logs()
    {
        return view('admin.logs.index', [
            'logs' => ParserLog::orderBy('id', 'desc')->paginate(),
        ]);
    }
}
