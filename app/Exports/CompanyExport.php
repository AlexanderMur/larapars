<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 16.10.2018
 * Time: 16:49
 */

namespace App\Exports;


use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompanyExport extends Export implements FromCollection, WithHeadings, WithMapping
{


    public $ids = [];

    public function __construct($ids)
    {

        $this->ids = $ids;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if($this->ids){
            return Company::whereIn('id',$this->ids)->get();
        }
        return Company::all();

    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Название',
            'Телефон',
            'Сайт',
            'Город',
            'Адрес',
        ];
    }

    /**
     * @param Company $company
     * @return array
     */
    public function map($company): array
    {

        return [
            $company->title,
            $company->phone,
            $company->site,
            $company->city,
            $company->address,
        ];
    }
}