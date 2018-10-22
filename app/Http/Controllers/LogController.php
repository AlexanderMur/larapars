<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 10.10.2018
 * Time: 14:33
 */

namespace App\Http\Controllers;


use App\DataTables\LogDataTable;
use App\ParserLog;
use Yajra\DataTables\Html\Builder;

class LogController
{
    /**
     * @var Builder
     */
    public $builder;

    public function __construct(Builder $builder)
    {

        $this->builder = $builder;
    }

    public function index(LogDataTable $dataTable)
    {
        if (request()->ajax()) {
            return $dataTable->ajax();
        }
        $html = $dataTable->html();
        return view('admin.logs.index', [
            'html' => $html,
        ]);
    }

    /**
     * @param ParserLog $log
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function details(ParserLog $log)
    {
        return response()->json(view('admin.logs.__details', [
            'log' => $log,
        ])->render());

    }
}