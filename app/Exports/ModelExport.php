<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ModelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Company::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Телефон',
            'Ссылка на страницу',
            'сайт',
            'заголовок',
            'Адрес',
            'Дата парсинга',
        ];
    }

    /**
     * @param Company $company
     * @return array
     */
    public function map($company): array
    {

        return [
            $company->id,
            $company->phone,
            $company->single_page_link,
            $company->site,
            $company->title,
            $company->address,
            $company->created_at,
        ];
    }
}
