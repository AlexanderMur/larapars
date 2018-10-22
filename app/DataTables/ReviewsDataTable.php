<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 22.10.2018
 * Time: 15:37
 */

namespace App\DataTables;


use App\Models\Review;
use Illuminate\Support\HtmlString;

class ReviewsDataTable extends DataTable
{
    public function query()
    {
        return Review::with(['parsed_company.company', 'donor'])
            ->select('reviews.*')
            ->leftJoin('parsed_companies','parsed_companies.id','=','reviews.parsed_company_id')
            ->leftJoin('companies as company','company.id','=','parsed_companies.company_id')
            ->where('good', '!=', null);
    }

    public function ajax()
    {
        xdebug_break();
        return $this->dataTables
            ->eloquent(
                $this->query()
            )
            ->editColumn('id', function (Review $review) {
                return new HtmlString("<input type='checkbox' value='$review->id' name='reviews[]'/>");
            })
            ->editColumn('company.title', function (Review $review) {
                if ($review->company) {
                    ob_start();
                    ?>
                    <b><a href="<?php echo route('companies.show', $review->company->id) ?>"><?php echo $review->company->title ?></a></b>
                    <br>
                    <a href="<?php echo $review->donor_link ?>" target="_blank">Перейти к странице донора</a>
                    <?php
                    return new HtmlString(ob_get_clean());
                }
                return null;
            })
            ->editColumn('text', function (Review $review) {
                ob_start();
                echo $review->text
                ?>
                <div class="actions">
                    <span class="model-edit"><a href="<?php echo route('reviews.edit', $review) ?>">Редактировать</a></span>
                    |
                    <span class="model-trash"><a
                            class="text-danger"
                            href="<?php echo route('reviews.destroy', $review) ?>"
                        >В корзину</a></span>
                </div>
                <?php
                return new HtmlString(ob_get_clean());
            })
            ->editColumn('good', function (Review $review) {
                ob_start();
                if ($review->good === true) {
                    ?>
                    <i class="fa fa-fw fa-2x fa-thumbs-up text-success"></i>
                    <?php
                }
                if ($review->good === false) {
                    ?>
                    <i class="fa fa-fw fa-2x fa-thumbs-down text-danger"></i>
                    <?php
                }

                return new HtmlString(ob_get_clean());
            })
            ->toJson();
    }

    public function html()
    {
        return $this->builder
            ->columns([
                'id'            => ['orderable' => false, 'title' => ''],
                'name'          => ['title' => __('company.name')],
                'title'         => ['title' => __('company.title')],
                'text'          => ['title' => __('company.text')],
                'good'          => ['width' => '1%', 'title' => __('company.good')],
                'rated_at'      => ['title' => __('company.rated_at')],
                'company.title' => ['title' => __('company.company.title')],
                'donor.title'   => ['title' => __('company.donor.title')],
            ])
            ->parameters([
                'order' => [[5, "desc"]],
            ]);
    }
}