<?php

namespace App\Http\Controllers;

use App\Components\ParserClass;
use App\Models\ParserTask;
use App\Services\ParserService;

class TaskController extends Controller
{
    /**
     * @var ParserService
     */
    public $parserService;
    public $parserClass;

    public function __construct(ParserService $parserService)
    {
        $this->parserService = $parserService;
        $this->parserClass   = new ParserClass();
    }

    public function resume(ParserTask $task){
        $task = $task->resume();
        return response()->json(['id'=>$task->id]);
    }
    public function pause(ParserTask $task){
        $task->setPausing();
        return response()->json('ok');
    }

}
