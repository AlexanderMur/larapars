<?php

namespace App\Http\Controllers;

use App\Exports\ModelExport;
use App\Models\Company;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function allData(){

        $companies = Company::with(['donor','reviews'])->get();
        return view('admin.companies.index',[
            'companies' => $companies,
        ]);
    }
    public function export(){
        return \Excel::download(new ModelExport,'model.xls');
    }
}
