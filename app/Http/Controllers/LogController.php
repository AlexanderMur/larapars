<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 10.10.2018
 * Time: 14:33
 */

namespace App\Http\Controllers;


use App\ParserLog;
use Illuminate\Support\HtmlString;
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

    public function index()
    {
        if (request()->ajax()) {
            return \DataTables
                ::eloquent(
                    ParserLog::query()
                )
                ->editColumn('message', function (ParserLog $log) {
                    ob_start();
                    if ($log->status == 'bold') {
                        ?>
                        <td>
                            <b><?php echo $log->message ?></b>
                            <a href="<?php echo $log->url ?>"><?php echo str_limit($log->url, 55) ?></a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td>
                            <?php echo $log->message ?>
                            <a href="<?php echo $log->url ?>" target="_blank"><?php echo str_limit($log->url, 55) ?></a>
                            <?php
                            if($log->status === 'ok'){
                                ?>
                                <span class="label label-info">OK</span>
                                <?php
                            }
                            ?>
                        </td>
                        <?php
                    }
                    return new HtmlString(ob_get_clean());
                })
                ->orderColumn('created_at','id $1')
                ->toJson();
        }
        $html = $this->builder
            ->columns([
                'created_at',
                'message',
                'url' => ['visible' => false],
            ])
            ->parameters([
                'order' => [[0, "desc"]],
            ])
            ->parameters([
                "lengthMenu" => [[20, 50, 100, 200, 500], [20, 50, 100, 200, 500],],
            ]);
        return view('admin.logs.index', [
            'html' => $html,
        ]);
    }
}