<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 10.10.2018
 * Time: 14:33
 */

namespace App\Http\Controllers;


use App\DataTables\LogDataTable;
use App\Models\ParserTask;
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
     * @param ParserTask $task
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function details(ParserTask $task)
    {
        $task = $task->load(['parsed_companies2.donor'=>function($query) use (&$task) {
            $query->withTaskStats($task->id);
        }]);
        return response()->json(view('admin.logs.__details', [
            'task' => $task,
        ])->render());

    }
}