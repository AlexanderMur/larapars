<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function allData(){

        $companies = Company::with(['donor','reviews'])->get();
        return view('admin.companies.index',[
            'companies' => $companies,
        ]);
    }
}
