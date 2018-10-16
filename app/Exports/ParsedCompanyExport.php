<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 16.10.2018
 * Time: 16:49
 */

namespace App\Exports;


use App\Models\Company;
use App\Models\ParsedCompany;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParsedCompanyExport extends Export implements FromCollection, WithHeadings, WithMapping
{
    public $ids = [];



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
            'Страница',
            'Сайт',
            'Город',
            'Адрес',
        ];
    }

    /**
     * @param ParsedCompany $company
     * @return array
     */
    public function map($company): array
    {

        return [
            $company->title,
            $company->phone,
            $company->donor_page,
            $company->site,
            $company->city,
            $company->address,
        ];
    }
}