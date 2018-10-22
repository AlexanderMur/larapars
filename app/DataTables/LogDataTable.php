<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 22.10.2018
 * Time: 15:37
 */

namespace App\DataTables;


use App\ParserLog;
use Illuminate\Support\HtmlString;

class LogDataTable extends DataTable
{
    public function query()
    {
        return ParserLog::query();
    }

    public function ajax()
    {
        return $this->dataTables
            ->eloquent(
                $this->query()
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
                        if ($log->status === 'ok') {
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
            ->orderColumn('created_at', 'id $1')
            ->toJson();
    }

    public function html()
    {
        return $this->builder
            ->columns([
                'created_at' => ['title' => __('company.created_at')],
                'message'    => ['title' => __('company.message')],
                'url'        => ['visible' => false],
            ])
            ->parameters([
                'order' => [[0, "desc"]],
            ]);
    }
}